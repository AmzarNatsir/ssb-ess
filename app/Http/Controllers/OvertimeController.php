<?php

namespace App\Http\Controllers;

use App\Models\Overtime;
use App\Models\Approval;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use App\Models\Karyawan;
use App\Helpers\HrdFunction;

class OvertimeController extends Controller
{
    /**
     * Display a listing of the user's overtime requests.
     */
    public function index()
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee data not found.');
        }

        // Submission tab – status_pengajuan IS NULL
        $pendingRequests = Overtime::with(['currentApproval.jabatan', 'approvals'])
            ->where('id_karyawan', $karyawan->id)
            ->where('status_pengajuan', 1) //pengajuan
            ->orderBy('tgl_pengajuan', 'desc')
            ->get();

        // History tab – status_pengajuan = 2 (Approved) or 3 (Rejected)
        $history = Overtime::with(['currentApproval.jabatan'])
            ->where('id_karyawan', $karyawan->id)
            ->whereIn('status_pengajuan', [2, 3]) //approved, rejected
            ->orderBy('tgl_pengajuan', 'desc')
            ->get();

        return view('overtime.index', compact('pendingRequests', 'history'));
    }

    /**
     * Show the form for creating a new overtime request.
     */
    public function create()
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee data not found.');
        }

        return view('overtime.create', compact('karyawan'));
    }

    /**
     * Store a newly created overtime request in storage.
     */
    public function store(Request $request)
    {
        try {
            $id_depat_karyawan = Karyawan::find(auth()->user()->karyawan->id)->id_departemen;
            $_uuid = Str::uuid();
            $group = 7; //Pengajuan Lembur
            $ifSet = HrdFunction::set_approval_cek($group, $id_depat_karyawan);
            if($ifSet > 0)
            {
                $user     = Auth::user();
                $karyawan = $user->karyawan;

                if (!$karyawan) {
                    return redirect()->back()->with('error', 'Employee data not found.');
                }
                $request->validate([
                    'tgl_pengajuan'            => 'required|date',
                    'jam_mulai'             => 'required|date_format:H:i',
                    'jam_selesai'           => 'required|date_format:H:i|after:jam_mulai',
                    'deskripsi_pekerjaan'  => 'required|string|max:1000',
                    'file_surat_lembur'     => 'required|file|mimes:jpg,jpeg,png|max:5120',
                ], [
                    'jam_selesai.after'     => 'End time must be after start time.',
                    'file_surat_lembur.mimes' => 'Only JPG, JPEG, and PNG images are allowed.',
                ]);

                $total_jam = $this->calculateTotalHours($request->jam_mulai, $request->jam_selesai);

                if ($total_jam < 1) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Minimum overtime duration is 1 hour.');
                }

                if ($total_jam > 8) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Maximum overtime duration is 8 hours.');
                }

                $filePath = $request->file('file_surat_lembur')->store('overtime_orders', 'public');

                Overtime::create([
                    'id_karyawan'          => $karyawan->id,
                    'tgl_pengajuan'        => $request->tgl_pengajuan,
                    'jam_mulai'            => $request->jam_mulai,
                    'jam_selesai'          => $request->jam_selesai,
                    'total_jam'            => $total_jam,
                    'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
                    'file_surat_perintah_lembur'    => $filePath,
                    'status_pengajuan'     => 1, //Pengajuan
                    'approval_key' => $_uuid,
                    'current_approval_id' => HrdFunction::set_approval_get_first($group, $id_depat_karyawan)
                ]);
                $arr_appr =  HrdFunction::set_approval_new($group, $id_depat_karyawan);
                foreach($arr_appr as $appr)
                {
                    $approval_active=0;
                    if($appr['approval_level']==1) {
                        $approval_active = 1;
                    }
                    \App\Models\Approval::create([
                        'approval_by_employee' => $appr['approval_by_employee'],
                        'approval_level' => $appr['approval_level'],
                        'approval_key' => $_uuid,
                        'approval_group' => $group, //Pengajuan Lembur
                        'approval_active' => $approval_active
                    ]);
                }
                return redirect()->route('overtime.index')
                    ->with('success', 'Overtime request submitted successfully.');

            } else {
                return redirect()->route('overtime.index')->with('error', 'The approval matrix has not been set up yet');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    /**
     * Show the form for editing an overtime request (only if status_pengupuan == null).
     */
    public function edit($id)
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        $overtime = Overtime::where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        if (!is_null($overtime->status_pengajuan)) {
            return redirect()->route('overtime.index')
                ->with('error', 'Only pending requests can be edited.');
        }

        return view('overtime.edit', compact('overtime', 'karyawan'));
    }

    /**
     * Update the specified overtime request.
     */
    public function update(Request $request, $id)
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        $overtime = Overtime::where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        if (!is_null($overtime->status_pengajuan)) {
            return redirect()->route('overtime.index')
                ->with('error', 'Only pending requests can be edited.');
        }

        $request->validate([
            'tgl_pengajuan'           => 'required|date',
            'jam_mulai'            => 'required|date_format:H:i',
            'jam_selesai'          => 'required|date_format:H:i|after:jam_mulai',
            'deskripsi_pekerjaan' => 'required|string|max:1000',
            'file_surat_perintah_lembur'    => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
        ], [
            'jam_selesai.after'       => 'End time must be after start time.',
            'file_surat_perintah_lembur.mimes' => 'Only JPG, JPEG, and PNG images are allowed.',
        ]);

        $total_jam = $this->calculateTotalHours($request->jam_mulai, $request->jam_selesai);

        if ($total_jam < 1) {
            return redirect()->back()->withInput()
                ->with('error', 'Minimum overtime duration is 1 hour.');
        }

        if ($total_jam > 8) {
            return redirect()->back()->withInput()
                ->with('error', 'Maximum overtime duration is 8 hours.');
        }

        $data = [
            'tgl_pengajuan'           => $request->tgl_pengajuan,
            'jam_mulai'            => $request->jam_mulai,
            'jam_selesai'          => $request->jam_selesai,
            'total_jam'            => $total_jam,
            'deskripsi_pekerjaan' => $request->deskripsi_pekerjaan,
        ];

        if ($request->hasFile('file_surat_perintah_lembur')) {
            // Delete old file
            if ($overtime->file_surat_perintah_lembur) {
                Storage::disk('public')->delete($overtime->file_surat_perintah_lembur);
            }
            $data['file_surat_perintah_lembur'] = $request->file('file_surat_perintah_lembur')->store('overtime_orders', 'public');
        }

        $overtime->update($data);

        return redirect()->route('overtime.index')
            ->with('success', 'Overtime request updated successfully.');
    }

    /**
     * Display the specified overtime request as JSON (for modal).
     */
    public function show($id)
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        $overtime = Overtime::with(['approvals.get_profil_employee.jabatan'])
            ->where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        $statusMap = [
            1 => 'Pengajuan',
            2 => 'Disetujui',
            3 => 'Ditolak',
        ];

        return response()->json([
            'success' => true,
            'data'    => [
                'id'                        => $overtime->id,
                'tgl_pengajuan'             => $overtime->tgl_pengajuan ? $overtime->tgl_pengajuan->format('d M Y') : '-',
                'jam_mulai'                 => substr($overtime->jam_mulai, 0, 5),
                'jam_selesai'               => substr($overtime->jam_selesai, 0, 5),
                'total_jam'                 => $overtime->total_jam,
                'deskripsi_pekerjaan'       => $overtime->deskripsi_pekerjaan,
                'file_surat_perintah_lembur' => $overtime->file_surat_perintah_lembur
                    ? '/storage/' . $overtime->file_surat_perintah_lembur
                    : null,
                'status_pengajuan'          => $overtime->status_pengajuan,
                'status_text'               => $statusMap[$overtime->status_pengajuan] ?? 'Unknown',
                'approvals'                 => $overtime->approvals->map(function ($appr) {
                    return [
                        'level'    => $appr->approval_level,
                        'name'     => $appr->get_profil_employee->nm_lengkap ?? 'Unknown',
                        'position' => $appr->get_profil_employee->jabatan->nm_jabatan ?? '-',
                        'status'   => $appr->approval_status == 1 ? 'Approved' : ($appr->approval_status == 2 ? 'Rejected' : 'Pending'),
                        'remark'   => $appr->approval_remark ?? '-',
                    ];
                }),
            ],
        ]);
    }

    /**
     * Cancel (delete) a pending overtime request.
     */
    public function destroy($id)
    {
        $user     = Auth::user();
        $karyawan = $user->karyawan;

        $overtime = Overtime::where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        if (!is_null($overtime->status_pengajuan)) {
            return redirect()->route('overtime.index')
                ->with('error', 'Only pending requests can be cancelled.');
        }

        if ($overtime->file_surat_perintah_lembur) {
            Storage::disk('public')->delete($overtime->file_surat_perintah_lembur);
        }

        //hapus hirarki persetujuan
        $check_data = Approval::where('approval_key', $overtime->approval_key)->get()->count();
        if($check_data > 0) {
            Approval::where('approval_key', $overtime->approval_key)->delete();
        }

        $overtime->delete();

        return redirect()->route('overtime.index')
            ->with('success', 'Overtime request cancelled successfully.');
    }

    /**
     * Calculate total hours between start and end time.
     */
    private function calculateTotalHours(string $start, string $end): float
    {
        $s = Carbon::createFromFormat('H:i', $start);
        $e = Carbon::createFromFormat('H:i', $end);
        return round($s->diffInMinutes($e) / 60, 2);
    }
}
