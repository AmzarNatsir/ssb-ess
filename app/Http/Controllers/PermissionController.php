<?php

namespace App\Http\Controllers;

use App\Helpers\HrdFunction;
use App\Helpers\HrdConstants;
use App\Models\Approval;
use App\Models\Karyawan;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PermissionController extends Controller
{
    /**
     * Display a listing of the user's permission requests.
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee data not found.');
        }

        // Pending Requests (sts_pengajuan = 1)
        $pendingRequests = Permission::with('jenisPermission')
            ->where('id_karyawan', $karyawan->id)
            ->where('sts_pengajuan', 1)
            ->orderBy('tgl_pengajuan', 'desc')
            ->get();

        // History (sts_pengajuan in 2, 3, 4)
        $history = Permission::with('jenisPermission')
            ->where('id_karyawan', $karyawan->id)
            ->whereIn('sts_pengajuan', [2, 3, 4])
            ->orderBy('tgl_pengajuan', 'desc')
            ->get();

        return view('permission.index', compact('pendingRequests', 'history'));
    }

    /**
     * Show the form for creating a new permission request.
     */
    public function create()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee data not found.');
        }

        $permissionTypes = \App\Models\JenisCuti::where('jenis_ci', 2)->where('status', 1)->get();

        return view('permission.create', compact('permissionTypes', 'karyawan'));
    }

    /**
     * Store a newly created permission request in storage.
     */
    public function store(Request $request)
    {
        try {
            $user = Auth::user();
            $karyawan = $user->karyawan;
            // dd($karyawan);
            $request->validate([
                'id_jenis_izin' => 'required|exists:mst_hrd_jenis_cuti_izin,id',
                'tgl_awal' => 'required|date',
                'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
                'ket_izin' => 'required|string',
            ]);

            $tgl_awal = \Carbon\Carbon::parse($request->tgl_awal);
            $tgl_akhir = \Carbon\Carbon::parse($request->tgl_akhir);
            $jumlah_hari = $tgl_awal->diffInDays($tgl_akhir) + 1;

            $id_depat_karyawan = Karyawan::find($karyawan->id)->id_departemen;
            $_uuid = Str::uuid();
            $group = 4;
            $ifSet = HrdFunction::set_approval_cek($group, $id_depat_karyawan);
            if($ifSet > 0)
            {
                Permission::create([
                    'id_karyawan' => $karyawan->id,
                    'id_jenis_izin' => $request->id_jenis_izin,
                    'tgl_pengajuan' => now(),
                    'tgl_awal' => $request->tgl_awal,
                    'tgl_akhir' => $request->tgl_akhir,
                    'jumlah_hari' => $jumlah_hari,
                    'sts_pengajuan' => 1, // Submission
                    'ket_izin' => $request->ket_izin,
                    'id_user' => $user->id,
                    'id_departemen' => $id_depat_karyawan,
                    'approval_key' => $_uuid,
                    'current_approval_id' => HrdFunction::set_approval_get_first($group, $id_depat_karyawan),
                    'is_draft' => 1 //pengajuan masih bisa diedit
                ]);
                $arr_appr =  HrdFunction::set_approval_new($group, $id_depat_karyawan);
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
                        'approval_group' => $group, //Pengajuan Cuti
                        'approval_active' => $approval_active
                    ]);
                }
                return redirect()->route('permission.index')->with('success', 'Permission application submitted successfully.');
            }

        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'Failed to submit permission request.');
        }
    }

    /**
     * Show the form for editing the specified permission request.
     */
    public function edit($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $permission = Permission::where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        if ($permission->sts_pengajuan != 1 || $permission->is_draft != 1) {
            return redirect()->route('permission.index')->with('error', 'Only draft requests can be edited.');
        }

        $permissionTypes = \App\Models\JenisCuti::where('jenis_ci', 2)->where('status', 1)->get();

        return view('permission.edit', compact('permission', 'permissionTypes', 'karyawan'));
    }

    /**
     * Update the specified permission request in storage.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $permission = Permission::where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        if ($permission->sts_pengajuan != 1 || $permission->is_draft != 1) {
            return redirect()->route('permission.index')->with('error', 'Only draft requests can be edited.');
        }

        $request->validate([
            'id_jenis_izin' => 'required|exists:mst_hrd_jenis_cuti_izin,id',
            'tgl_awal' => 'required|date',
            'tgl_akhir' => 'required|date|after_or_equal:tgl_awal',
            'ket_izin' => 'required|string',
        ]);

        $tgl_awal = \Carbon\Carbon::parse($request->tgl_awal);
        $tgl_akhir = \Carbon\Carbon::parse($request->tgl_akhir);
        $jumlah_hari = $tgl_awal->diffInDays($tgl_akhir) + 1;

        $permission->update([
            'id_jenis_izin' => $request->id_jenis_izin,
            'tgl_awal' => $request->tgl_awal,
            'tgl_akhir' => $request->tgl_akhir,
            'jumlah_hari' => $jumlah_hari,
            'ket_izin' => $request->ket_izin,
        ]);

        return redirect()->route('permission.index')->with('success', 'Permission application updated successfully.');
    }

    /**
     * Display the specified permission request.
     */
    public function show($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        $permission = Permission::with(['jenisPermission', 'approvals.get_profil_employee.jabatan'])
            ->where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $permission->id,
                'tgl_pengajuan' => date('d M Y', strtotime($permission->tgl_pengajuan)),
                'jenis_izin' => $permission->jenisPermission->nm_jenis_ci ?? 'N/A',
                'tgl_awal' => date('d M Y', strtotime($permission->tgl_awal)),
                'tgl_akhir' => date('d M Y', strtotime($permission->tgl_akhir)),
                'jumlah_hari' => $permission->jumlah_hari,
                'ket_izin' => $permission->ket_izin,
                'status' => HrdConstants::STATUS_IZIN[$permission->sts_pengajuan] ?? 'Unknown',
                'approvals' => $permission->approvals->map(function($appr) {
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
        $update = Permission::find($id);
        $update->sts_pengajuan = 4; //pengajuan batal
        $update->is_draft = 2;
        $update->save();
        //hapus hirarki persetujuan
        $check_data = Approval::where('approval_key', $update->approval_key)->get()->count();
        if($check_data > 0) {
            Approval::where('approval_key', $update->approval_key)->delete();
        }
        return redirect()->route('permission.index')->with('success', 'Permission application cancelled successfully.');
    }

}
