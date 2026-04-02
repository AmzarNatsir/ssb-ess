<?php

namespace App\Http\Controllers;

use App\Models\TrainingHead;
use App\Models\TrainingDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TrainingController extends Controller
{
    /**
     * Display a listing of the training records.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        if (!$karyawan) {
            return redirect()->back()->with('error', 'Employee data not found.');
        }

        $currentYear = date('Y');

        // Fetch IDs of TrainingHead where the user is a participant
        $trainingIds = TrainingDetail::where('id_karyawan', $karyawan->id)->pluck('id_head');

        // Get all trainings for this participant
        $allTrainings = TrainingHead::with(['get_nama_pelatihan', 'get_pelaksana'])
            ->whereIn('id', $trainingIds)
            ->orderBy('tanggal_awal', 'desc')
            ->get();

        // Split into current year and history
        $currentYearTrainings = $allTrainings->filter(function ($item) use ($currentYear) {
            return date('Y', strtotime($item->tanggal_awal)) == $currentYear;
        });

        $historyTrainings = $allTrainings->filter(function ($item) use ($currentYear) {
            return date('Y', strtotime($item->tanggal_awal)) < $currentYear;
        });

        return view('training.index', compact('currentYearTrainings', 'historyTrainings'));
    }

    /**
     * Get detail of the training session including participant list.
     */
    public function detail($id)
    {
        try {
            $user = Auth::user();
            $karyawan = $user->karyawan;

            // Ensure the user is a participant of this training
            $isParticipant = TrainingDetail::where('id_head', $id)->where('id_karyawan', $karyawan->id)->exists();
            if (!$isParticipant) {
                return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
            }

            $training = TrainingHead::with(['get_nama_pelatihan', 'get_pelaksana', 'get_peserta.get_karyawan.jabatan'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => [
                    'nomor' => $training->nomor ?? '-',
                    'nama_pelatihan' => $training->get_nama_pelatihan->nama_pelatihan ?? $training->nama_pelatihan,
                    'vendor' => $training->get_pelaksana->nama_lembaga ?? ($training->nama_vendor ?? '-'),
                    'kategori' => $training->kategori ?: 'General',
                    'durasi' => $training->durasi,
                    'tempat' => $training->tempat_pelaksanaan ?? '-',
                    'tanggal_awal' => $training->tanggal_awal ? date('d M Y', strtotime($training->tanggal_awal)) : '-',
                    'tanggal_sampai' => $training->tanggal_sampai ? date('d M Y', strtotime($training->tanggal_sampai)) : '-',
                    'pukul_awal' => $training->pukul_awal ? date('H:i', strtotime($training->pukul_awal)) : '-',
                    'pukul_sampai' => $training->pukul_sampai ? date('H:i', strtotime($training->pukul_sampai)) : '-',
                    'peserta' => $training->get_peserta->map(function ($detail) {
                        return [
                            'name' => $detail->get_karyawan->nm_karyawan ?? $detail->get_karyawan->nm_lengkap ?? '-',
                            'nik' => $detail->get_karyawan->nik ?? '-',
                            'jabatan' => $detail->get_karyawan->jabatan->nm_jabatan ?? '-',
                        ];
                    })
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Get existing post-training report data.
     */
    public function getReport($id)
    {
        try {
            $user = Auth::user();
            $karyawan = $user->karyawan;

            $detail = TrainingDetail::where('id_head', $id)
                ->where('id_karyawan', $karyawan->id)
                ->firstOrFail();

            $evidenceUrl = null;
            $isImage = false;
            if ($detail->evidence_pasca && file_exists(public_path($detail->evidence_pasca))) {
                $evidenceUrl = asset($detail->evidence_pasca);
                $ext = strtolower(pathinfo($detail->evidence_pasca, PATHINFO_EXTENSION));
                $isImage = in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
            }

            return response()->json([
                'success' => true,
                'has_report' => !empty($detail->pasca) && $detail->pasca == 1,
                'data' => [
                    'tujuan_pelatihan_pasca'  => $detail->tujuan_pelatihan_pasca,
                    'uraian_materi_pasca'     => $detail->uraian_materi_pasca,
                    'tindak_lanjut_pasca'     => $detail->tindak_lanjut_pasca,
                    'dampak_pasca'            => $detail->dampak_pasca,
                    'penutup_pasca'           => $detail->penutup_pasca,
                    'evidence_url'            => $evidenceUrl,
                    'is_image'                => $isImage,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Submit post-training report.
     */
    public function submitReport(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $karyawan = $user->karyawan;

            $detail = TrainingDetail::where('id_head', $id)->where('id_karyawan', $karyawan->id)->firstOrFail();

            $detail->tujuan_pelatihan_pasca = $request->input('tujuan_pelatihan_pasca');
            $detail->uraian_materi_pasca = $request->input('uraian_materi_pasca');
            $detail->tindak_lanjut_pasca = $request->input('tindak_lanjut_pasca');
            $detail->dampak_pasca = $request->input('dampak_pasca');
            $detail->penutup_pasca = $request->input('penutup_pasca');
            $detail->pasca = 1; // Mark as submitted

            if ($request->hasFile('evidence_pasca')) {
                $file = $request->file('evidence_pasca');
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $destinationPath = public_path('uploads/training_evidence');
                if (!file_exists($destinationPath)) {
                    mkdir($destinationPath, 0755, true);
                }
                $file->move($destinationPath, $filename);
                $detail->evidence_pasca = 'uploads/training_evidence/' . $filename;
            }

            $detail->save();

            return response()->json(['success' => true, 'message' => 'Laporan berhasil disimpan.']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
