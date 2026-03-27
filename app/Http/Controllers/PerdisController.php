<?php

namespace App\Http\Controllers;

use App\Models\Perdis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PerdisController extends Controller
{
    /**
     * Display a listing of official travel data.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee data not found.');
        }

        // Get filter values or default to current month/year
        $month = $request->get('month', date('n'));
        $year = $request->get('year', date('Y'));

        // Fetch official travel data
        $perdisList = Perdis::where('id_karyawan', $karyawan->id)
            ->whereMonth('tgl_perdis', $month)
            ->whereYear('tgl_perdis', $year)
            ->orderBy('tgl_perdis', 'desc')
            ->get();

        // Pass available months and years for filter dropdowns
        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = date('F', mktime(0, 0, 0, $m, 1));
        }

        $years = range(date('Y') - 5, date('Y') + 1);

        return view('perdis.index', compact('perdisList', 'month', 'year', 'months', 'years'));
    }

    /**
     * Get details of a business trip for the accountability modal.
     */
    public function getDetails($id)
    {
        $perdis = Perdis::with(['fasilitas.masterFasilitas', 'approvals.get_profil_employee'])->find($id);

        if (!$perdis) {
            return response()->json(['error' => 'Data not found'], 404);
        }

        // Prepare data for the view
        $approvals = $perdis->approvals->map(function ($app) {
            return [
                'level' => $app->approval_level,
                'approver' => $app->get_profil_employee ? $app->get_profil_employee->nama : 'N/A',
                'status' => $app->approval_status == 1 ? 'Approved' : ($app->approval_status == 2 ? 'Rejected' : 'Pending'),
                'date' => $app->approval_date ? Carbon::parse($app->approval_date)->format('d M Y') : '-',
                'remark' => $app->approval_remark ?? '-'
            ];
        });

        $facilities = $perdis->fasilitas->map(function ($fac) {
            return [
                'id' => $fac->id,
                'item' => $fac->masterFasilitas ? $fac->masterFasilitas->nm_fasilitas : 'N/A',
                'hari' => $fac->hari,
                'biaya' => $fac->biaya,
                'sub_total' => $fac->sub_total,
                'realisasi' => $fac->realisasi,
                'file_1' => $fac->file_1,
                'file_2' => $fac->file_2
            ];
        });

        return response()->json([
            'perdis' => $perdis,
            'approvals' => $approvals,
            'facilities' => $facilities
        ]);
    }

    /**
     * Update realization and upload documents for accountability.
     */
    public function updateAccountability(Request $request, $id)
    {
        $request->validate([
            'realisasi' => 'required|array',
            'file_1' => 'nullable|array',
            'file_2' => 'nullable|array',
            'file_1.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'file_2.*' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        foreach ($request->realisasi as $facId => $value) {
            $facility = \App\Models\PerdisFasilitas::find($facId);
            if ($facility) {
                // Remove formatting (e.g., 1,000,000 -> 1000000)
                $cleanValue = (float)str_replace(['.', ','], '', $value);
                $facility->realisasi = $cleanValue;

                // Handle file uploads
                if ($request->hasFile("file_1.$facId")) {
                    // Delete old file if exists
                    if ($facility->file_1) {
                        \Storage::disk('public')->delete($facility->file_1);
                    }
                    
                    $file1 = $request->file("file_1.$facId");
                    $filename1 = time() . '_1_' . $file1->getClientOriginalName();
                    $path1 = 'perdis_documents/' . $filename1;
                    \Storage::disk('public')->putFileAs('perdis_documents', $file1, $filename1);
                    $facility->file_1 = $path1;
                }

                if ($request->hasFile("file_2.$facId")) {
                    // Delete old file if exists
                    if ($facility->file_2) {
                        \Storage::disk('public')->delete($facility->file_2);
                    }

                    $file2 = $request->file("file_2.$facId");
                    $filename2 = time() . '_2_' . $file2->getClientOriginalName();
                    $path2 = 'perdis_documents/' . $filename2;
                    \Storage::disk('public')->putFileAs('perdis_documents', $file2, $filename2);
                    $facility->file_2 = $path2;
                }

                $facility->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Realization updated successfully.']);
    }
}
