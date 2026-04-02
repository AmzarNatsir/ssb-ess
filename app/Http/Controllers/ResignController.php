<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Resign;
use App\Models\Approval;
use App\Helpers\Hrdfunction;

class ResignController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Data karyawan tidak ditemukan.');
        }

        $resigns = Resign::with(['current_approve.jabatan', 'exitInterview'])
            ->where('id_karyawan', $karyawan->id)
            ->orderBy('created_at', 'desc')
            ->get();

        $exitInterviews = \App\Models\ExitInterviews::with(['getPengajuan', 'get_current_approve.jabatan'])
            ->whereHas('getPengajuan', function($q) use($karyawan) {
                $q->where('id_karyawan', $karyawan->id);
            })
            ->orderBy('created_at', 'desc')
            ->get();

        return view('resign.index', compact('resigns', 'exitInterviews'));
    }

    public function create()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->route('resign.index')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $tgl_eff_resign = Carbon::today()->addDays(30)->format('Y-m-d');
        
        return view('resign.create', compact('tgl_eff_resign'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $id_depat_karyawan = $karyawan->id_departemen;
        $_uuid = Str::uuid();
        $group = 15; // Group ID for Resign
        $ifSet = Hrdfunction::set_approval_cek($group, $id_depat_karyawan);
        
        if($ifSet == 0) {
            return redirect()->back()->with('error', 'Data approval belum diatur (Matriks Persetujuan). Hubungi HRD.');
        }

        $request->validate([
            'alasan_resign' => 'required|string',
            'file_surat_resign' => 'required|mimes:pdf|max:2048',
        ]);

        DB::beginTransaction();
        try {
            $tgl_eff_resign = Carbon::today()->addDays(30)->format('Y-m-d');
            $filePath = null;

            if ($request->hasFile('file_surat_resign')) {
                $uploadPath = storage_path('app/public/resign_dokumen');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file = $request->file('file_surat_resign');
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move($uploadPath, $filename);
                $filePath = 'storage/resign_dokumen/' . $filename;
            }

            $resign = Resign::create([
                'id_karyawan'         => $karyawan->id,
                'tgl_eff_resign'      => $tgl_eff_resign,
                'alasan_resign'       => $request->alasan_resign,
                'approval_key'        => $_uuid,
                'create_by'           => $user->id,
                'current_approval_id' => Hrdfunction::set_approval_get_first($group, $id_depat_karyawan),
                'is_draft'            => 1,
                'sts_pengajuan'       => 1,
                'file_surat_resign'   => $filePath,
            ]);

            $arr_appr = Hrdfunction::set_approval_new($group, $id_depat_karyawan);
            foreach($arr_appr as $appr)
            {
                $approval_active = 0;
                if($appr['approval_level'] == 1) {
                    $approval_active = 1;
                }
                Approval::create([
                    'approval_by_employee' => $appr['approval_by_employee'],
                    'approval_level'       => $appr['approval_level'],
                    'approval_key'         => $_uuid,
                    'approval_group'       => $group,
                    'approval_active'      => $approval_active
                ]);
            }

            DB::commit();
            return redirect()->route('resign.index')->with('success', 'Pengajuan resign berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $resign = Resign::where('id_karyawan', $karyawan->id)->findOrFail($id);

        $approvals = Approval::with('get_profil_employee.jabatan')
            ->where('approval_key', $resign->approval_key)
            ->orderBy('approval_level', 'asc')
            ->get()
            ->map(function($app) {
                return [
                    'level'     => $app->approval_level,
                    'name'      => $app->get_profil_employee->nm_lengkap ?? '-',
                    'jabatan'   => $app->get_profil_employee->jabatan->nm_jabatan ?? '-',
                    'status'    => $app->approval_status, // 0=Wait, 1=Approve, 2=Reject
                    'tgl_app'   => $app->updated_at && $app->approval_status != 0 ? $app->updated_at->format('d M Y H:i') : '-',
                    'is_active' => $app->approval_active
                ];
            });

        $statusMap = [
            1 => 'Pengajuan',
            2 => 'Disetujui',
            3 => 'Ditolak',
            4 => 'Dibatalkan',
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'tgl_pengajuan'     => $resign->created_at->format('d M Y'),
                'tgl_eff_resign'    => Carbon::parse($resign->tgl_eff_resign)->format('d M Y'),
                'alasan_resign'     => $resign->alasan_resign,
                'status_label'      => $statusMap[$resign->sts_pengajuan] ?? '-',
                'file_surat_resign' => $resign->file_surat_resign,
                'approvals'         => $approvals,
            ]
        ]);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $resign = Resign::where('id_karyawan', $karyawan->id)->findOrFail($id);

        if ($resign->is_draft != 1 || $resign->sts_pengajuan != 1) {
            return redirect()->route('resign.index')->with('error', 'Pengajuan ini tidak dapat diedit karena sudah diproses.');
        }

        return view('resign.edit', compact('resign'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $resign = Resign::where('id_karyawan', $karyawan->id)->findOrFail($id);

        if ($resign->is_draft != 1 || $resign->sts_pengajuan != 1) {
            return redirect()->route('resign.index')->with('error', 'Pengajuan ini tidak dapat diedit karena sudah diproses.');
        }

        $request->validate([
            'alasan_resign' => 'required|string',
            'file_surat_resign' => 'nullable|mimes:pdf|max:2048',
        ]);

        try {
            if ($request->hasFile('file_surat_resign')) {
                // Delete old file
                if ($resign->file_surat_resign) {
                    $oldFilename = str_replace('storage/', '', $resign->file_surat_resign);
                    $oldPath = storage_path('app/public/' . $oldFilename);
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                }

                $uploadPath = storage_path('app/public/resign_dokumen');
                if (!file_exists($uploadPath)) {
                    mkdir($uploadPath, 0755, true);
                }

                $file = $request->file('file_surat_resign');
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move($uploadPath, $filename);
                $resign->file_surat_resign = 'storage/resign_dokumen/' . $filename;
            }

            $resign->alasan_resign = $request->alasan_resign;
            $resign->save();

            return redirect()->route('resign.index')->with('success', 'Data pengajuan resign berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $resign = Resign::where('id_karyawan', $karyawan->id)->findOrFail($id);

        if ($resign->is_draft != 1 || $resign->sts_pengajuan != 1) {
            return response()->json(['success' => false, 'message' => 'Pengajuan ini tidak dapat dibatalkan karena sudah diproses.'], 400);
        }

        DB::beginTransaction();
        try {
            // Delete file if exists
            if ($resign->file_surat_resign) {
                $filename = str_replace('storage/', '', $resign->file_surat_resign);
                $fullPath = storage_path('app/public/' . $filename);
                if (file_exists($fullPath)) {
                    unlink($fullPath);
                }
            }

            // Delete Approval
            if ($resign->approval_key) {
                Approval::where('approval_key', $resign->approval_key)->delete();
            }

            // Delete Resign
            $resign->delete();

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Pengajuan resign berhasil dibatalkan dan dihapus.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }
}
