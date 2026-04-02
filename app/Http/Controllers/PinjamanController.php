<?php

namespace App\Http\Controllers;

use App\Models\PinjamanKaryawan;
use App\Models\PinjamanKaryawanDokumen;
use App\Models\PinjamanKaryawanMutasi;
use App\Models\Approval;
use App\Models\Karyawan;
use App\Helpers\Hrdfunction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PinjamanController extends Controller
{
    /**
     * List all loans for the authenticated employee.
     */
    public function index()
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $pinjamans = PinjamanKaryawan::with(['currentApprover.jabatan'])
            ->where('id_karyawan', $karyawan->id)
            ->orderBy('tgl_pengajuan', 'desc')
            ->get();

        $hasActiveLoan = $this->hasActiveLoan($karyawan->id);

        return view('pinjaman.index', compact('pinjamans', 'hasActiveLoan'));
    }

    /**
     * Show the loan application form.
     */
    public function create()
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->route('pinjaman.index')->with('error', 'Data karyawan tidak ditemukan.');
        }

        if ($this->hasActiveLoan($karyawan->id)) {
            return redirect()->route('pinjaman.index')
                ->with('error', 'Anda masih memiliki pinjaman yang sedang berjalan atau dalam proses pengajuan.');
        }

        // Load relationships for profile display
        $karyawan->load(['jabatan', 'departemen']);

        $gajiPokok = (float) ($karyawan->gaji_pokok ?? 0);
        $tunjangan = (float) ($karyawan->tunjangan ?? 0);

        // Panjar: 50% gaji pokok
        $maxPanjar = $gajiPokok * 0.5;

        // PKK: max 35% gaji pokok
        $maxPkk = $gajiPokok * 0.35;

        return view('pinjaman.create', compact('karyawan', 'gajiPokok', 'tunjangan', 'maxPanjar', 'maxPkk'));
    }

    /**
     * Store a new loan application.
     */
    public function store(Request $request)
    {
        $id_depat_karyawan = Karyawan::find(auth()->user()->karyawan->id)->id_departemen;
        $_uuid = Str::uuid();
        $group = 13; // group approval pinjaman
        $ifSet = Hrdfunction::set_approval_cek($group, $id_depat_karyawan);
        if($ifSet == 0)
        {
            return redirect()->back()->with('error', 'Data approval belum diatur (Matriks Persetujuan). Hubungi HRD.');
        }
        $request->validate([
            'kategori'          => 'required|in:1,2',
            'alasan_pengajuan'  => 'required|string',
            'nominal_apply'     => 'required|numeric|min:1',
            'tenor_apply'       => 'required|integer|min:1',
        ]);

        $user     = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $kategori      = (int) $request->kategori;
        $nominalApply  = (float) $request->nominal_apply;
        $tenorApply    = (int) $request->tenor_apply;
        $angsuran      = $tenorApply > 0 ? round($nominalApply / $tenorApply, 2) : $nominalApply;
        $gajiPokok     = (float) ($karyawan->gaji_pokok ?? 0);

        // Business rule validation
        if ($kategori === 1) {
            // Panjar Gaji: fixed 50% gaji pokok, tenor = 1
            $maxNominal   = $gajiPokok * 0.5;
            $tenorApply   = 1;
            $nominalApply = $maxNominal;
            $angsuran     = $nominalApply;

            if ($nominalApply <= 0) {
                return redirect()->back()->with('error', 'Gaji pokok belum diatur. Hubungi HRD.');
            }
        } else {
            // PKK: angsuran max 35% gaji pokok
            $maxNominal = $gajiPokok * 0.35;
            if ($angsuran > $maxNominal) {
                return redirect()->back()->withInput()->with('error', 'Angsuran per bulan melebihi batas maksimal 35% dari gaji pokok.');
            }
        }

        DB::beginTransaction();
        try {
            // 1. Create head record
            $pinjaman = PinjamanKaryawan::create([
                'id_karyawan'       => $karyawan->id,
                'tgl_pengajuan'     => now()->format('Y-m-d'),
                'kategori'          => $kategori,
                'alasan_pengajuan'  => $request->alasan_pengajuan,
                'nominal_apply'     => $nominalApply,
                'tenor_apply'       => $tenorApply,
                'angsuran'          => $angsuran,
                'status_pengajuan'  => 1,
                'is_draft'          => 1,
                'aktif'             => 'y',
                'approval_key'      => $_uuid,
                'current_approval_id' => hrdfunction::set_approval_get_first($group, $id_depat_karyawan),
            ]);

            // 2. Generate mutasi (installment schedule)
            for ($i = 1; $i <= $tenorApply; $i++) {
                PinjamanKaryawanMutasi::create([
                    'id_head'    => $pinjaman->id,
                    'tanggal'    => now()->addMonths($i - 1)->format('Y-m-d'),
                    'nominal'    => $angsuran,
                    'status'     => null,
                    'bayar_aktif' => ($i === 1) ? 1 : 0,
                ]);
            }

            //set tgl jatuh tempo
            Hrdfunction::generate_duedate_pinjaman_karyawan($pinjaman->id, date('Y-m-d'));

            // 3. Upload documents (PKK only)
            if ($kategori === 2 && $request->hasFile('dokumen')) {
                $uploadPath = storage_path('app/public/pinjaman_dokumen');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                foreach ($request->file('dokumen') as $index => $file) {
                    $filename = time() . '_' . $index . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                    $file->move($uploadPath, $filename);

                    $keterangan = $request->keterangan_dokumen[$index] ?? null;

                    PinjamanKaryawanDokumen::create([
                        'id_head'       => $pinjaman->id,
                        'file_dokumen'  => 'storage/pinjaman_dokumen/' . $filename,
                        'keterangan'    => $keterangan,
                    ]);
                }
            }

            $arr_appr =  Hrdfunction::set_approval_new($group, $id_depat_karyawan);
            foreach($arr_appr as $appr)
            {
                $approval_active=0;
                if($appr['approval_level']==1) {
                    $approval_active = 1;
                }
                Approval::create([
                    'approval_by_employee' => $appr['approval_by_employee'],
                    'approval_level' => $appr['approval_level'],
                    'approval_key' => $_uuid,
                    'approval_group' => $group, //Pengajuan Pinjaman
                    'approval_active' => $approval_active
                ]);
            }

            DB::commit();

            return redirect()->route('pinjaman.index')
                ->with('success', 'Pengajuan pinjaman berhasil disimpan. Menunggu persetujuan HRD.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()
                ->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * AJAX: Return detail of a single loan.
     */
    public function detail($id)
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        $pinjaman = PinjamanKaryawan::with(['mutasi' => function($q) {
            $q->orderBy('tanggal', 'asc');
        }, 'dokumen', 'pembayaran' => function($q) {
            $q->orderBy('tanggal', 'asc');
        }])
            ->where('id_karyawan', $karyawan->id)
            ->findOrFail($id);

        $statusMap = [
            1 => 'Pengajuan',
            2 => 'Disetujui',
            3 => 'Ditolak',
            4 => 'Dibatalkan',
        ];

        $nominal = (float)($pinjaman->nominal_acc ?? $pinjaman->nominal_apply);
        $pembayarans = $pinjaman->pembayaran;
        $totalPayment = $pembayarans->sum('nominal');
        $outstanding = $nominal - $totalPayment;
        if ($outstanding < 0) $outstanding = 0;

        $pembayaranCount = $pembayarans->count();
        $sisaTenor = $pinjaman->tenor_apply - $pembayaranCount;
        if ($sisaTenor < 0) $sisaTenor = 0;

        $structuredMutasi = [];
        $mutasiList = $pinjaman->mutasi;

        foreach ($mutasiList as $index => $m) {
            $isPaid   = $index < $pembayaranCount;
            $isActive = $index === $pembayaranCount;

            if ($isPaid) {
                // Already paid -> refer to actual paid nominal or scheduled nominal based on restructuring
                // We'll use the scheduled nominal or the actual payment? The prompt implies structuring the remaining mutasi.
                // Let's keep the scheduled nominal for history, but marked as Paid.
                $structuredMutasi[] = [
                    'tanggal'    => $m->tanggal ? date('d M Y', strtotime($m->tanggal)) : '-',
                    'nominal'    => $m->nominal, // The originally scheduled nominal
                    'bayar_aktif'=> 0,
                    'status'     => 1, // Paid
                ];
            } else {
                // Remaining tenor -> restructure based on outstanding
                $newAngsuran = $sisaTenor > 0 ? round($outstanding / $sisaTenor, 2) : 0;
                $structuredMutasi[] = [
                    'tanggal'    => $m->tanggal ? date('d M Y', strtotime($m->tanggal)) : '-',
                    'nominal'    => $newAngsuran,
                    'bayar_aktif'=> $isActive ? 1 : 0,
                    'status'     => null, // Unpaid
                ];
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'kategori_label'   => $pinjaman->kategori == 1 ? 'Panjar Gaji' : 'Pinjaman Kesejahteraan Karyawan (PKK)',
                'alasan_pengajuan' => $pinjaman->alasan_pengajuan,
                'tgl_pengajuan'    => date('d M Y', strtotime($pinjaman->tgl_pengajuan)),
                'status_label'     => $statusMap[$pinjaman->status_pengajuan] ?? '-',
                'nominal_apply'    => $nominal,
                'tenor_apply'      => $pinjaman->tenor_apply,
                'angsuran'         => $pinjaman->angsuran,
                'outstanding'      => $outstanding,
                'total_payment'    => $totalPayment,
                'sisa_tenor'       => $sisaTenor,
                'pembayaran'       => $pembayarans->map(fn($p) => [
                    'tanggal' => date('d M Y', strtotime($p->tanggal)),
                    'nominal' => $p->nominal,
                ]),
                'mutasi'           => collect($structuredMutasi),
                'dokumen'          => $pinjaman->dokumen->map(fn($d) => [
                    'file'       => $d->file_dokumen,
                    'keterangan' => $d->keterangan,
                ]),
            ]
        ]);
    }

    /**
     * AJAX: Return employee salary info for dynamic form calculation.
     */
    public function gajiInfo()
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }

        $gajiPokok = (float) ($karyawan->gaji_pokok ?? 0);

        return response()->json([
            'success'    => true,
            'gaji_pokok' => $gajiPokok,
            'max_panjar' => round($gajiPokok * 0.5, 2),
            'max_pkk'    => round($gajiPokok * 0.35, 2),
        ]);
    }

    /**
     * AJAX: Cancel and delete loan application.
     */
    public function cancel($id)
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return response()->json(['success' => false, 'message' => 'Data karyawan tidak ditemukan.'], 404);
        }

        $pinjaman = PinjamanKaryawan::where('id_karyawan', $karyawan->id)->find($id);

        if (!$pinjaman) {
            return response()->json(['success' => false, 'message' => 'Data pinjaman tidak ditemukan.'], 404);
        }

        if ($pinjaman->is_draft != 1 || $pinjaman->status_pengajuan != 1) {
            return response()->json(['success' => false, 'message' => 'Pinjaman ini tidak dapat dibatalkan karena sudah diproses.'], 400);
        }

        DB::beginTransaction();
        try {
            // Delete Mutasi
            PinjamanKaryawanMutasi::where('id_head', $pinjaman->id)->delete();

            // Delete Dokumen and files
            $dokumens = PinjamanKaryawanDokumen::where('id_head', $pinjaman->id)->get();
            foreach ($dokumens as $dokumen) {
                // file_dokumen e.g. "storage/pinjaman_dokumen/filename.ext"
                $filename = str_replace('storage/', '', $dokumen->file_dokumen);
                $fullPath = storage_path('app/public/' . $filename);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
                $dokumen->delete();
            }

            // Delete Approval
            if ($pinjaman->approval_key) {
                Approval::where('approval_key', $pinjaman->approval_key)->delete();
            }

            // Delete Pinjaman Head
            $pinjaman->delete();

            DB::commit();

            return response()->json(['success' => true, 'message' => 'Pengajuan pinjaman berhasil dibatalkan dan dihapus.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    /**
     * Check if employee has an ongoing loan.
     */
    private function hasActiveLoan($karyawanId)
    {
        return PinjamanKaryawan::where('id_karyawan', $karyawanId)
            ->where(function($query) {
                $query->where('status_pengajuan', 1) // Sedang diajukan
                      ->orWhere(function($q) {       // Disetujui tapi belum lunas
                          $q->where('status_pengajuan', 2)
                            ->whereHas('mutasi', function($qm) {
                                $qm->whereNull('status');
                            });
                      });
            })
            ->exists();
    }
}
