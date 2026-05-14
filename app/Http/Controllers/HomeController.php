<?php

namespace App\Http\Controllers;

use App\Models\MemoInternal;
use App\Models\TrainingHead;
use App\Models\SetupHariLibur;
use App\Models\Karyawan;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Memo Internal - status=1, tgl_post desc, limit 10
        $memos = MemoInternal::where('status', 1)
            ->orderBy('tgl_post', 'desc')
            ->limit(10)
            ->get();

        // Pelatihan - status_pelatihan >= 2, tahun berjalan, dikelompokkan per bulan
        $currentYear = date('Y');
        $trainingsByMonth = [];

        $allTrainings = TrainingHead::where('status_pelatihan', '>=', 2)
            ->whereYear('tanggal_awal', $currentYear)
            ->orderBy('tanggal_awal', 'asc')
            ->get();

        foreach ($allTrainings as $training) {
            $monthNum  = date('m', strtotime($training->tanggal_awal));
            $monthName = date('F', strtotime($training->tanggal_awal));
            if (!isset($trainingsByMonth[$monthNum])) {
                $trainingsByMonth[$monthNum] = ['name' => $monthName, 'items' => []];
            }
            $trainingsByMonth[$monthNum]['items'][] = $training;
        }

        // Hari Libur untuk calendar (tahun berjalan)
        $holidays = SetupHariLibur::whereYear('tanggal', $currentYear)
            ->get()
            ->keyBy('tanggal');

        // Karyawan ulang tahun bulan ini
        $currentMonth = date('m');
        $birthdayKaryawan = Karyawan::whereMonth('tgl_lahir', $currentMonth)
            ->whereNotNull('tgl_lahir')
            ->orderByRaw('DAY(tgl_lahir) ASC')
            ->get();

        return view('index', compact(
            'memos',
            'trainingsByMonth',
            'holidays',
            'birthdayKaryawan',
            'currentYear',
            'currentMonth'
        ));
    }
}
