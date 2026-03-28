<?php

namespace App\Http\Controllers;

use App\Models\WarningLetter;
use App\Models\ReprimandLetter;
use App\Models\Approval;
use App\Helpers\HrdFunction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemorandumController extends Controller
{
    /**
     * Display a listing of personal warning and reprimand letters.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee data not found.');
        }

        $selectedMonth = $request->get('month', date('m'));
        $selectedYear = $request->get('year', date('Y'));

        // Warning Letters (SP) - Filter: sts_pengajuan = 2 (Approved/Final)
        $warningLetters = WarningLetter::with('jenisSpDisetujui')
            ->where('id_karyawan', $karyawan->id)
            ->where('sts_pengajuan', 2)
            ->whereMonth('tgl_sp', $selectedMonth)
            ->whereYear('tgl_sp', $selectedYear)
            ->orderBy('tgl_sp', 'desc')
            ->get();

        // Reprimand Letters (ST) - Filter: status_pengajuan = 2 (Approved/Final)
        $reprimandLetters = ReprimandLetter::with('jenisPelanggaran')
            ->where('id_karyawan', $karyawan->id)
            ->where('status_pengajuan', 2)
            ->whereMonth('tanggal_kejadian', $selectedMonth)
            ->whereYear('tanggal_kejadian', $selectedYear)
            ->orderBy('tanggal_kejadian', 'desc')
            ->get();

        $months = [
            '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April',
            '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August',
            '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December'
        ];

        $years = range(date('Y'), date('Y') - 5);

        return view('memorandum.index', compact('warningLetters', 'reprimandLetters', 'selectedMonth', 'selectedYear', 'months', 'years'));
    }

    public function print_sp($id)
    {
        $dt_sp = WarningLetter::with('karyawan.jabatan', 'jenisSpDisetujui')->find($id);
        // Assuming generic Approval model is used for SP approvals
        // If there's a specific table/model, we might need to adjust this.
        // For now, let's use Approval or check if WarningLetter has a relationship.
        $approval = Approval::with(['get_profil_employee'])->where('approval_key', $dt_sp->approval_key)->orderBy('approval_level')->get();

        $kop_surat = HrdFunction::get_kop_surat();
        $witdhColumn = 100 / ($approval->count() + 1);
        
        $pdf = Pdf::loadView('memorandum.print_sp', compact('dt_sp', 'approval', 'kop_surat', 'witdhColumn'));
        $filename = 'SP_' . str_replace(['/', '\\'], '_', $dt_sp->no_sp) . '.pdf';
        return $pdf->stream($filename);
    }

    public function verify($type, $id)
    {
        if ($type == 'st') {
            $dt_st = ReprimandLetter::with(['karyawan.jabatan', 'jenisPelanggaran'])->findOrFail($id);
            $approval = Approval::with(['get_profil_employee.jabatan'])->where('approval_key', $dt_st->approval_key)->orderBy('approval_level')->get();
            return view('memorandum.verify_sp', [
                'type' => 'st',
                'dt' => $dt_st,
                'approval' => $approval
            ]);
        }

        // Default to SP
        $dt_sp = WarningLetter::with(['karyawan.jabatan', 'jenisSpDisetujui'])->findOrFail($id);
        $approval = Approval::with(['get_profil_employee.jabatan'])->where('approval_key', $dt_sp->approval_key)->orderBy('approval_level')->get();
        return view('memorandum.verify_sp', [
            'type' => 'sp',
            'dt' => $dt_sp,
            'approval' => $approval
        ]);
    }

    public function st_detail($id)
    {
        try {
            $st = ReprimandLetter::with(['karyawan.jabatan', 'jenisPelanggaran'])->findOrFail($id);
            $approvals = Approval::with(['get_profil_employee.jabatan'])
                ->where('approval_key', $st->approval_key)
                ->orderBy('approval_level')
                ->get();

            return response()->json([
                'success' => true,
                'data' => [
                    'id' => $st->id,
                    'no_st' => $st->no_st,
                    'tanggal_kejadian' => $st->tanggal_kejadian ? $st->tanggal_kejadian->format('d M Y') : '-',
                    'waktu_kejadian' => $st->waktu_kejadian ? date('H:i', strtotime($st->waktu_kejadian)) : '-',
                    'tempat_kejadian' => $st->tempat_kejadian,
                    'jenis_pelanggaran' => $st->jenisPelanggaran->jenis_pelanggaran ?? 'N/A',
                    'akibat' => $st->akibat,
                    'tindakan' => $st->tindakan,
                    'rekomendasi' => $st->rekomendasi,
                    'komentar_pelanggar' => $st->komentar_pelanggar,
                    'status' => $st->status_pengajuan == 2 ? 'Approved' : 'Rejected',
                    'approvals' => $approvals->map(function ($appr) {
                        return [
                            'level' => $appr->approval_level,
                            'name' => $appr->get_profil_employee->nm_lengkap ?? 'N/A',
                            'position' => $appr->get_profil_employee->jabatan->nm_jabatan ?? '-',
                            'status' => $appr->approval_active == 1 ? 'Pending' : ($appr->approval_date ? 'Processed' : 'Waiting'),
                            'label_status' => $appr->approval_active == 1 ? 'Pending' : ($appr->approval_status == 1 ? 'Approved' : 'Rejected'),
                            'date' => $appr->approval_date ? date('d M Y H:i', strtotime($appr->approval_date)) : '-',
                            'remark' => $appr->approval_remark ?? '-'
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    //st print
    public function print_st($id)
    {
        $dt_st = ReprimandLetter::with([
            'karyawan.jabatan', 
            'karyawan.departemen',
            'jenisPelanggaran', 
            'diajukanOleh.jabatan'
        ])->findOrFail($id);
        
        $approval = Approval::with(['get_profil_employee.jabatan'])->where('approval_key', $dt_st->approval_key)->orderBy('approval_level')->get();

        $kop_surat = HrdFunction::get_kop_surat();
        $witdhColumn = 100 / ($approval->count() + 2); // Added 2 for Maker and Employee
        
        $pdf = Pdf::loadView('memorandum.print_st', compact('dt_st', 'approval', 'kop_surat', 'witdhColumn'));
        $filename = 'ST_' . str_replace(['/', '\\'], '_', $dt_st->no_st) . '.pdf';
        return $pdf->stream($filename);
    }
}
