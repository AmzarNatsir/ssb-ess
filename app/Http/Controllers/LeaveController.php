<?php

namespace App\Http\Controllers;

use App\Models\Leave;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\JenisCuti;
use App\Models\Karyawan;
use App\Models\Approval;
use App\Helpers\HrdFunction;
use App\Helpers\HrdConstants;
use Illuminate\Support\Str;
use Carbon\Carbon;

class LeaveController extends Controller
{
    /**
     * Display a listing of the user's leave requests.
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee data not found.');
        }
        // Pending Requests (sts_pengajuan = 1)
        $pendingRequests = Leave::with(['jenisCuti', 'get_current_approve', 'approvals.get_profil_employee'])
            ->where('id_karyawan', $karyawan->id)
            ->where('sts_pengajuan', 1)
            ->orderBy('tgl_pengajuan', 'desc')
            ->get();

        // History (sts_pengajuan = 2 (Approved), 3 (Rejected))
        $history = Leave::with(['jenisCuti', 'get_current_approve'])
            ->where('id_karyawan', $karyawan->id)
            ->whereIn('sts_pengajuan', [2, 3, 4])
            ->orderBy('tgl_pengajuan', 'desc')
            ->get();

        // Count active leave: approved (sts_pengajuan = 2) and end date >= today
        $count_aktif_cuti = Leave::where('id_karyawan', $karyawan->id)
                                ->where('sts_pengajuan', 2)
                                ->where('tgl_akhir', '>=', date("Y-m-d"))
                                ->count();

        return view('leave.index', compact('pendingRequests', 'history', 'count_aktif_cuti'));
    }

    /**
     * Show the form for creating a new leave request.
     */
    public function create()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee data not found.');
        }

        // Validasi masa kerja minimal 1 tahun
        $isEligible = false;
        if ($karyawan->tgl_masuk) {
            $joinDate = Carbon::parse($karyawan->tgl_masuk);
            $isEligible = $joinDate->diffInYears(Carbon::now()) >= 1;
        }

        $leaveTypes = JenisCuti::leave()->where('status', 1)->get();
        $group = 3;
        $approvalMatrix = HrdFunction::set_approval_new($group, $karyawan->id_departemen)->load('getPejabat.jabatan');
        $isApprovalMatrixConfigured = $approvalMatrix->isNotEmpty();

        return view('leave.create', compact('leaveTypes', 'karyawan', 'approvalMatrix', 'isApprovalMatrixConfigured', 'isEligible'));
    }

    /**
     * Store a newly created leave request in storage.
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->withInput()->with('error', 'Data karyawan tidak ditemukan.');
        }

        // Validasi masa kerja minimal 1 tahun
        if ($karyawan->tgl_masuk) {
            $joinDate = Carbon::parse($karyawan->tgl_masuk);
            if ($joinDate->diffInYears(Carbon::now()) < 1) {
                return redirect()->back()->with('error', 'Anda belum bisa mengajukan cuti. Masa kerja minimal 1 tahun diperlukan.');
            }
        }

        $request->validate([
            'id_jenis_cuti' => 'required|exists:mst_hrd_jenis_cuti_izin,id',
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
            'ket_cuti' => 'required|string',
        ]);

        $tgl_awal = Carbon::parse($request->tgl_awal);
        $tgl_akhir = Carbon::parse($request->tgl_akhir);
        $jumlah_hari = $tgl_awal->diffInDays($tgl_akhir) + 1;

        $jenisCuti = JenisCuti::findOrFail($request->id_jenis_cuti);
        $remaining = $this->calculateRemainingEntitlement($karyawan->id, $jenisCuti);

        if ($jumlah_hari > $remaining) {
            return redirect()->back()->withInput()->with('error', 'Durasi cuti melebihi sisa hak cuti yang tersedia.');
        }

        $id_depat_karyawan = $karyawan->id_departemen;
        $_uuid = Str::uuid();
        $group = 3;
        $ifSet = HrdFunction::set_approval_cek($group, $id_depat_karyawan);

        if ($ifSet <= 0) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Matriks persetujuan pengajuan cuti belum diatur. Silakan hubungi HRD/Administrator.');
        }

        Leave::create([
            'id_karyawan' => $karyawan->id,
            'id_jenis_cuti' => $request->id_jenis_cuti,
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir,
            'tgl_pengajuan' => now(),
            'sts_pengajuan' => 1,
            'jumlah_hari' => $jumlah_hari,
            'ket_cuti' => $request->ket_cuti,
            'id_user' => $user->id,
            'id_departemen' => $id_depat_karyawan,
            'approval_key' => $_uuid,
            'current_approval_id' => HrdFunction::set_approval_get_first($group, $id_depat_karyawan),
            'is_draft' => 1
        ]);

        $arr_appr = HrdFunction::set_approval_new($group, $id_depat_karyawan);
        foreach ($arr_appr as $appr) {
            $approval_active = 0;
            if ($appr['approval_level'] == 1) {
                $approval_active = 1;
            }

            Approval::create([
                'approval_by_employee' => $appr['approval_by_employee'],
                'approval_level' => $appr['approval_level'],
                'approval_key' => $_uuid,
                'approval_group' => $group,
                'approval_active' => $approval_active
            ]);
        }

        return redirect()->route('leave.index')->with('success', 'Pengajuan cuti berhasil dikirim.');
    }

    /**
     * Show the form for editing the specified leave request.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $leave = Leave::where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        if ($leave->sts_pengajuan != 1) {
            return redirect()->route('leave.index')->with('error', 'Only pending requests can be edited.');
        }

        $leaveTypes = JenisCuti::leave()->where('status', 1)->get();

        return view('leave.edit', compact('leave', 'leaveTypes', 'karyawan'));
    }

    /**
     * Update the specified leave request in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $leave = Leave::where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        if ($leave->sts_pengajuan != 1) {
            return redirect()->route('leave.index')->with('error', 'Only pending requests can be edited.');
        }

        $request->validate([
            'id_jenis_cuti' => 'required|exists:mst_hrd_jenis_cuti_izin,id',
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
            'ket_cuti' => 'required|string',
        ]);

        $tgl_awal = Carbon::parse($request->tgl_awal);
        $tgl_akhir = Carbon::parse($request->tgl_akhir);
        $jumlah_hari = $tgl_awal->diffInDays($tgl_akhir) + 1;

        // Entitlement Validation (exclude current record)
        $jenisCuti = JenisCuti::findOrFail($request->id_jenis_cuti);
        $remaining = $this->calculateRemainingEntitlement($karyawan->id, $jenisCuti, $id);

        if ($jumlah_hari > $remaining) {
            return redirect()->back()->withInput()->with('error', 'Leave duration exceeds remaining entitlement.');
        }

        $leave->update([
            'id_jenis_cuti' => $request->id_jenis_cuti,
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir,
            'jumlah_hari' => $jumlah_hari,
            'ket_cuti' => $request->ket_cuti,
        ]);

        return redirect()->route('leave.index')->with('success', 'Leave application updated successfully.');
    }

    /**
     * Display the specified leave request.
     */
    public function show($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $leave = Leave::with(['jenisCuti', 'approvals.get_profil_employee.jabatan', 'get_karyawan_pengganti.jabatan'])
            ->where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $leave->id,
                'tgl_pengajuan' => date('d M Y', strtotime($leave->tgl_pengajuan)),
                'jenis_cuti' => $leave->jenisCuti->nm_jenis_ci ?? 'N/A',
                'tgl_awal' => date('d M Y', strtotime($leave->tgl_awal)),
                'tgl_akhir' => date('d M Y', strtotime($leave->tgl_akhir)),
                'jumlah_hari' => $leave->jumlah_hari,
                'ket_cuti' => $leave->ket_cuti,
                'status' => HrdConstants::STATUS_CUTI[$leave->sts_pengajuan] ?? 'Unknown',
                'pengganti' => $leave->get_karyawan_pengganti ? [
                    'name' => $leave->get_karyawan_pengganti->nm_lengkap,
                    'position' => $leave->get_karyawan_pengganti->jabatan->nm_jabatan ?? '-'
                ] : null,
                'approvals' => $leave->approvals->map(function($appr) {
                    return [
                        'level' => $appr->approval_level,
                        'name' => $appr->get_profil_employee->nm_lengkap ?? 'N/A',
                        'position' => $appr->get_profil_employee->jabatan->nm_jabatan ?? '-',
                        'status' => $appr->approval_active == 1 ? 'Pending' : ($appr->approval_date ? 'Processed' : 'Waiting'),
                        'remark' => $appr->approval_remark ?? '-'
                    ];
                })
            ]
        ]);
    }

    public function cancel(Request $request, $id)
    {
        $update = Leave::find($id);
        $update->sts_pengajuan = 4; //pengajuan batal
        $update->is_draft = 2;
        $update->save();
        //hapus hirarki persetujuan
        $check_data = Approval::where('approval_key', $update->approval_key)->get()->count();
        if($check_data > 0) {
            Approval::where('approval_key', $update->approval_key)->delete();
        }
        return redirect()->route('leave.index')->with('success', 'Leave application cancelled successfully.');
    }

    /**
     * Get remaining entitlement for AJAX.
     */
    public function getRemainingEntitlement(Request $request, $id_jenis_cuti)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        $jenisCuti = JenisCuti::findOrFail($id_jenis_cuti);
        $excludeId = $request->query('exclude_id');

        $remaining = $this->calculateRemainingEntitlement($karyawan->id, $jenisCuti, $excludeId);

        return response()->json([
            'remaining' => $remaining
        ]);
    }

    /**
     * Helper to calculate remaining entitlement.
     */
    private function calculateRemainingEntitlement($id_karyawan, $jenisCuti, $excludeId = null)
    {
        $currentYear = now()->year;

        $query = Leave::where('id_karyawan', $id_karyawan)
            ->where('id_jenis_cuti', $jenisCuti->id)
            ->whereIn('sts_pengajuan', [2]) // Count approved
            ->whereYear('tgl_awal', $currentYear);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        $taken = $query->sum('jumlah_hari');

        return max(0, $jenisCuti->lama_cuti - $taken);
    }
}
