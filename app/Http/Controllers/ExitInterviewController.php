<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Models\Resign;
use App\Models\ExitInterviews;
use App\Models\Approval;
use App\Helpers\Hrdfunction;

class ExitInterviewController extends Controller
{
    public function create($id_resign)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->route('resign.index')->with('error', 'Data karyawan tidak ditemukan.');
        }

        $resign = Resign::with('karyawan')->where('id_karyawan', $karyawan->id)->findOrFail($id_resign);

        if ($resign->sts_pengajuan != 2) {
            return redirect()->route('resign.index')->with('error', 'Pengajuan Resign belum disetujui, Anda belum bisa mengisi Exit Interview.');
        }

        if ($resign->exitInterview) {
            return redirect()->route('resign.index')->with('error', 'Anda sudah mengisi formulir Exit Interview untuk pengajuan ini.');
        }

        return view('exit_interview.create', compact('resign'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        $id_resign = $request->id_head;
        $resign = Resign::where('id_karyawan', $karyawan->id)->findOrFail($id_resign);

        if ($resign->sts_pengajuan != 2) {
            return redirect()->route('resign.index')->with('error', 'Pengajuan Resign belum disetujui.');
        }

        $id_depat_karyawan = $karyawan->id_departemen;
        $_uuid = Str::uuid();
        $group = 16; // Group ID for Exit Interview
        $ifSet = Hrdfunction::set_approval_cek($group, $id_depat_karyawan);
        
        if($ifSet == 0) {
            return redirect()->back()->with('error', 'Data approval belum diatur (Matriks Persetujuan). Hubungi HRD.');
        }

        $request->validate([
            'id_head' => 'required|exists:hrd_resign,id',
            'jawaban_1' => 'required|string',
            'jawaban_2' => 'required|string',
            'jawaban_3' => 'required|string',
            'jawaban_4' => 'required|string',
            'jawaban_5' => 'required|string',
            'jawaban_6' => 'required|string',
            'jawaban_7' => 'required|string',
            'jawaban_8' => 'required|string', // Bagus/Cukup/Kurang
            'jawaban_8_1' => 'required|string',
            'jawaban_9' => 'required|string',
            // Jawaban skala 1-4 validation
            'jawaban_9_1' => 'required|in:1,2,3,4',
            'jawaban_9_2' => 'required|in:1,2,3,4',
            'jawaban_9_3' => 'required|in:1,2,3,4',
            'jawaban_9_4' => 'required|in:1,2,3,4',
            'jawaban_9_5' => 'required|in:1,2,3,4',
            'jawaban_9_6' => 'required|in:1,2,3,4',
            'jawaban_9_7' => 'required|in:1,2,3,4',
            'jawaban_9_8' => 'required|in:1,2,3,4',
            'jawaban_9_9' => 'required|in:1,2,3,4',
            'jawaban_10' => 'required|string',
        ]);

        DB::beginTransaction();
        try {
            $exitInterview = ExitInterviews::create([
                'id_head'             => $request->id_head,
                'jawaban_1'           => $request->jawaban_1,
                'jawaban_1_1'         => $request->jawaban_1_1,
                'jawaban_1_2'         => $request->jawaban_1_2,
                'jawaban_1_3'         => str_replace('.', '', $request->jawaban_1_3),
                'jawaban_2'           => $request->jawaban_2,
                'jawaban_3'           => $request->jawaban_3,
                'jawaban_4'           => $request->jawaban_4,
                'jawaban_5'           => $request->jawaban_5,
                'jawaban_6'           => $request->jawaban_6,
                'jawaban_6_1'         => $request->jawaban_6_1, // Date
                'jawaban_6_2'         => $request->jawaban_6_2, // Date
                'jawaban_7'           => $request->jawaban_7,
                'jawaban_8'           => $request->jawaban_8,
                'jawaban_8_1'         => $request->jawaban_8_1,
                'jawaban_9'           => $request->jawaban_9,
                'jawaban_9_1'         => $request->jawaban_9_1,
                'jawaban_9_2'         => $request->jawaban_9_2,
                'jawaban_9_3'         => $request->jawaban_9_3,
                'jawaban_9_4'         => $request->jawaban_9_4,
                'jawaban_9_5'         => $request->jawaban_9_5,
                'jawaban_9_6'         => $request->jawaban_9_6,
                'jawaban_9_7'         => $request->jawaban_9_7,
                'jawaban_9_8'         => $request->jawaban_9_8,
                'jawaban_9_9'         => $request->jawaban_9_9,
                'jawaban_10'          => $request->jawaban_10,
                'approval_key'        => $_uuid,
                'create_by'           => $user->id,
                'current_approval_id' => Hrdfunction::set_approval_get_first($group, $id_depat_karyawan),
                'is_draft'            => 1,
                'sts_pengajuan'       => 1,
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
            return redirect()->route('resign.index')->with('success', 'Formulir Exit Interview berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        // Ensure the exitinterview belongs to the user's resign head
        $exitInterview = ExitInterviews::with([
            'getPengajuan',
            'get_current_approve'
        ])->whereHas('getPengajuan', function($q) use($karyawan) {
            $q->where('id_karyawan', $karyawan->id);
        })->findOrFail($id);

        $approvals = Approval::with('get_profil_employee.jabatan')
            ->where('approval_key', $exitInterview->approval_key)
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
                'tgl_pengajuan' => $exitInterview->created_at->format('d M Y'),
                'status_label'  => $statusMap[$exitInterview->sts_pengajuan] ?? '-',
                'detail'        => $exitInterview,
                'approvals'     => $approvals,
            ]
        ]);
    }

    public function edit($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $exitInterview = ExitInterviews::with([
            'getPengajuan',
            'get_current_approve'
        ])->whereHas('getPengajuan', function($q) use($karyawan) {
            $q->where('id_karyawan', $karyawan->id);
        })->findOrFail($id);

        if ($exitInterview->is_draft != 1 || $exitInterview->sts_pengajuan != 1) {
            return redirect()->route('resign.index')->with('error', 'Pengajuan ini tidak dapat diedit karena sudah diproses.');
        }

        return view('exit_interview.edit', compact('exitInterview'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $exitInterview = ExitInterviews::whereHas('getPengajuan', function($q) use($karyawan) {
            $q->where('id_karyawan', $karyawan->id);
        })->findOrFail($id);

        if ($exitInterview->is_draft != 1 || $exitInterview->sts_pengajuan != 1) {
            return redirect()->route('resign.index')->with('error', 'Pengajuan ini tidak dapat diedit karena sudah diproses.');
        }

        $request->validate([
            'jawaban_1' => 'required|string',
            'jawaban_2' => 'required|string',
            'jawaban_3' => 'required|string',
            'jawaban_4' => 'required|string',
            'jawaban_5' => 'required|string',
            'jawaban_6' => 'required|string',
            'jawaban_7' => 'required|string',
            'jawaban_8' => 'required|string', // Bagus/Cukup/Kurang
            'jawaban_8_1' => 'required|string',
            'jawaban_9' => 'required|string',
            'jawaban_9_1' => 'required|in:1,2,3,4',
            'jawaban_9_2' => 'required|in:1,2,3,4',
            'jawaban_9_3' => 'required|in:1,2,3,4',
            'jawaban_9_4' => 'required|in:1,2,3,4',
            'jawaban_9_5' => 'required|in:1,2,3,4',
            'jawaban_9_6' => 'required|in:1,2,3,4',
            'jawaban_9_7' => 'required|in:1,2,3,4',
            'jawaban_9_8' => 'required|in:1,2,3,4',
            'jawaban_9_9' => 'required|in:1,2,3,4',
            'jawaban_10' => 'required|string',
        ]);

        try {
            $exitInterview->update([
                'jawaban_1'           => $request->jawaban_1,
                'jawaban_1_1'         => $request->jawaban_1_1,
                'jawaban_1_2'         => $request->jawaban_1_2,
                'jawaban_1_3'         => str_replace('.', '', $request->jawaban_1_3),
                'jawaban_2'           => $request->jawaban_2,
                'jawaban_3'           => $request->jawaban_3,
                'jawaban_4'           => $request->jawaban_4,
                'jawaban_5'           => $request->jawaban_5,
                'jawaban_6'           => $request->jawaban_6,
                'jawaban_6_1'         => $request->jawaban_6_1, // Date
                'jawaban_6_2'         => $request->jawaban_6_2, // Date
                'jawaban_7'           => $request->jawaban_7,
                'jawaban_8'           => $request->jawaban_8,
                'jawaban_8_1'         => $request->jawaban_8_1,
                'jawaban_9'           => $request->jawaban_9,
                'jawaban_9_1'         => $request->jawaban_9_1,
                'jawaban_9_2'         => $request->jawaban_9_2,
                'jawaban_9_3'         => $request->jawaban_9_3,
                'jawaban_9_4'         => $request->jawaban_9_4,
                'jawaban_9_5'         => $request->jawaban_9_5,
                'jawaban_9_6'         => $request->jawaban_9_6,
                'jawaban_9_7'         => $request->jawaban_9_7,
                'jawaban_9_8'         => $request->jawaban_9_8,
                'jawaban_9_9'         => $request->jawaban_9_9,
                'jawaban_10'          => $request->jawaban_10,
            ]);

            return redirect()->route('resign.index')->with('success', 'Data Exit Interview berhasil diperbarui.');

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function cancel($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $exitInterview = ExitInterviews::whereHas('getPengajuan', function($q) use($karyawan) {
            $q->where('id_karyawan', $karyawan->id);
        })->findOrFail($id);

        if ($exitInterview->is_draft != 1 || $exitInterview->sts_pengajuan != 1) {
            return response()->json(['success' => false, 'message' => 'Detail ini tidak dapat dibatalkan karena sudah diproses.'], 400);
        }

        DB::beginTransaction();
        try {
            if ($exitInterview->approval_key) {
                Approval::where('approval_key', $exitInterview->approval_key)->delete();
            }

            $exitInterview->delete();
            DB::commit();
            
            return response()->json(['success' => true, 'message' => 'Pengajuan Exit Interview berhasil dibatalkan dan dihapus.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }
}
