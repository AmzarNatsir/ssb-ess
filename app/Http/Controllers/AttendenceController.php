<?php

namespace App\Http\Controllers;

use App\Models\Attendence;
use App\Models\Leave;
use App\Models\Permission;
use App\Models\SetupHariLibur;
use App\Helpers\HrdConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AttendenceController extends Controller
{
    /**
     * Display the attendance calendar.
     */
    public function index()
    {
        return view('attendence.index');
    }

    /**
     * Fetch attendance data for the calendar.
     */
    public function getAttendenceData(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan || !$karyawan->nik_lama) {
            return response()->json([]);
        }

        // Fetch attendance records based on nik_lama
        $attendances = Attendence::where('nik_lama', $karyawan->nik_lama)
            ->whereBetween('tanggal', [$request->start, $request->end])
            ->get();

        // Fetch Holidays
        $holidays = SetupHariLibur::whereBetween('tanggal', [$request->start, $request->end])->get();
        $holidayDates = $holidays->pluck('tanggal')->toArray();

        $events = [];

        foreach ($attendances as $record) {
            $jam = substr($record->jam, 0, 5); // "08:00"
            $time = str_replace(':', '.', $jam); // "08.00"
            $status = $record->status;
            $location = $record->lokasi_id == 1 ? 'Head Office' : 'Factory';
            
            $is_in = str_contains($status, 'C/Masuk');
            $is_out = str_contains($status, 'C/Keluar');

            $label = "??";
            if ($is_in) {
                if ($jam <= "11:00") {
                    $label = "IN";
                } elseif ($jam >= "14:00" && $jam < "17:00") {
                    $label = "IN-After Break";
                } else {
                    $label = "IN";
                }
            } elseif ($is_out) {
                if ($jam > "11:00" && $jam < "14:00") {
                    $label = "Break";
                } elseif ($jam >= "17:00") {
                    $label = "OUT";
                } else {
                    $label = "OUT";
                }
            }

            $color = '#6ce4ad'; 
            if ($label == "Break") $color = '#e4e26c';
            if ($label == "OUT") $color = '#dc2007';

            $textColor = ($label == "OUT") ? '#ffffff' : '#064e3b';
            
            $title = "{$time} :: {$label}";

            $events[] = [
                'id'    => 'attn_'.$record->id,
                'title' => $title,
                'start' => $record->tanggal . 'T' . $record->jam,
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'textColor'       => $textColor,
                'extendedProps'   => [
                    'status_label' => $label,
                    'time'   => $jam,
                    'date'   => Carbon::parse($record->tanggal)->format('d M Y'),
                    'location' => $location
                ]
            ];
        }

        // Fetch Approved Leaves
        $leaves = Leave::where('id_karyawan', $karyawan->id)
            ->where('sts_pengajuan', 2) // Approved
            ->where(function($query) use ($request) {
                $query->whereBetween('tgl_awal', [$request->start, $request->end])
                      ->orWhereBetween('tgl_akhir', [$request->start, $request->end]);
            })
            ->get();

        foreach ($leaves as $leave) {
            $period = CarbonPeriod::create($leave->tgl_awal, $leave->tgl_akhir);
            $leaveTypeName = $leave->jenisCuti ? $leave->jenisCuti->nama_ci : 'Cuti';
            
            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                if ($dateStr < $request->start || $dateStr > $request->end) continue;

                $events[] = [
                    'id'    => 'leave_'.$leave->id.'_'.$dateStr,
                    'title' => "Cuti - " . $leaveTypeName,
                    'start' => $dateStr,
                    'allDay' => true,
                    'backgroundColor' => '#3b82f6',
                    'borderColor'     => '#3b82f6',
                    'textColor'       => '#ffffff',
                    'extendedProps'   => [
                        'status_label' => 'Cuti',
                        'time'   => 'All Day',
                        'date'   => $date->format('d M Y'),
                        'location' => 'N/A'
                    ]
                ];
            }
        }

        // Fetch Approved Permissions
        $permissions = Permission::where('id_karyawan', $karyawan->id)
            ->where('sts_pengajuan', 2) // Approved
            ->where(function($query) use ($request) {
                $query->whereBetween('tgl_awal', [$request->start, $request->end])
                      ->orWhereBetween('tgl_akhir', [$request->start, $request->end]);
            })
            ->get();

        foreach ($permissions as $perm) {
            $period = CarbonPeriod::create($perm->tgl_awal, $perm->tgl_akhir);
            $permTypeName = $perm->jenisPermission ? $perm->jenisPermission->nama_ci : 'Izin';

            foreach ($period as $date) {
                $dateStr = $date->format('Y-m-d');
                if ($dateStr < $request->start || $dateStr > $request->end) continue;

                $events[] = [
                    'id'    => 'perm_'.$perm->id.'_'.$dateStr,
                    'title' => "Izin - " . $permTypeName,
                    'start' => $dateStr,
                    'allDay' => true,
                    'backgroundColor' => '#8b5cf6',
                    'borderColor'     => '#8b5cf6',
                    'textColor'       => '#ffffff',
                    'extendedProps'   => [
                        'status_label' => 'Izin',
                        'time'   => 'All Day',
                        'date'   => $date->format('d M Y'),
                        'location' => 'N/A'
                    ]
                ];
            }
        }

        foreach ($holidays as $hday) {
            $events[] = [
                'id'    => 'hday_'.$hday->id,
                'title' => "Libur - " . $hday->keterangan,
                'start' => $hday->tanggal,
                'allDay' => true,
                'backgroundColor' => '#ef4444',
                'borderColor'     => '#ef4444',
                'textColor'       => '#ffffff',
                'extendedProps'   => [
                    'status_label' => 'Libur',
                    'time'   => 'All Day',
                    'date'   => Carbon::parse($hday->tanggal)->format('d M Y'),
                    'location' => 'Public Holiday'
                ]
            ];
        }

        return response()->json($events);
    }
}
