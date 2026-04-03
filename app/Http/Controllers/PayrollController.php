<?php

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\PayrollHeader;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PayrollController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee details not found.');
        }

        $currentYear = date('Y');
        
        // Fetch payroll for the current employee in the current year, joined with approved headers
        $payrolls = Payroll::where('id_karyawan', $karyawan->id)
            ->where('tahun', $currentYear)
            ->whereIn('bulan', function($query) use ($currentYear) {
                $query->select('bulan')
                    ->from('hrd_payroll_header')
                    ->where('tahun', $currentYear)
                    ->where('status_pengajuan', 2);
            })
            ->orderBy('bulan', 'desc')
            ->get();

        return view('payroll.index', compact('payrolls', 'currentYear'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee details not found.');
        }

        $payroll = Payroll::where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        return view('payroll.show', compact('payroll', 'karyawan'));
    }

    public function print($id)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee details not found.');
        }

        $payroll = Payroll::where('id', $id)
            ->where('id_karyawan', $karyawan->id)
            ->firstOrFail();

        return view('payroll.print', compact('payroll', 'karyawan'));
    }
}
