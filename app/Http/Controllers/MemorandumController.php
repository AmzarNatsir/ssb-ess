<?php

namespace App\Http\Controllers;

use App\Models\WarningLetter;
use App\Models\ReprimandLetter;
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
}
