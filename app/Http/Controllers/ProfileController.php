<?php

namespace App\Http\Controllers;

use App\Models\Jabatan;
use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * Display the user's profile settings page.
     */
    public function index()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;
        
        return view('profile-settings', compact('user', 'karyawan'));
    }

    /**
     * Display the user's profile details page.
     */
    public function show()
    {
        $user = Auth::user();
        $karyawan = $user->karyawan;

        // Daftar bawahan: telusuri SELURUH tingkatan di bawah jabatan user login
        // mengikuti garis komando (mst_hrd_jabatan.id_gakom -> id jabatan atasan).
        $bawahan = collect();
        if ($karyawan && $karyawan->id_jabatan) {
            // Peta jabatan -> daftar jabatan langsung di bawahnya (anak).
            $childrenByParent = Jabatan::whereNotNull('id_gakom')
                ->get(['id', 'id_gakom'])
                ->groupBy('id_gakom');

            // BFS menurun dari jabatan user, kumpulkan id jabatan + level kedalaman.
            $levelByJabatan = [];
            $queue = [[$karyawan->id_jabatan, 0]];
            while (!empty($queue)) {
                [$jabatanId, $level] = array_shift($queue);
                foreach ($childrenByParent->get($jabatanId, collect()) as $child) {
                    // Hindari loop bila data garis komando melingkar.
                    if (isset($levelByJabatan[$child->id])) {
                        continue;
                    }
                    $levelByJabatan[$child->id] = $level + 1;
                    $queue[] = [$child->id, $level + 1];
                }
            }

            if (!empty($levelByJabatan)) {
                $bawahan = Karyawan::with(['jabatan', 'departemen'])
                    ->whereIn('id_jabatan', array_keys($levelByJabatan))
                    ->whereIn('id_status_karyawan', [1, 2, 3, 7])
                    ->get()
                    ->each(function ($k) use ($levelByJabatan) {
                        $k->level_bawahan = $levelByJabatan[$k->id_jabatan] ?? 1;
                    })
                    // Urutkan: nama departemen -> level (asc) -> nama, agar setelah
                    // di-group per departemen, isinya runtut menaik per level.
                    ->sortBy([
                        fn ($a, $b) => strcasecmp(
                            $a->departemen->nm_dept ?? 'zzz',
                            $b->departemen->nm_dept ?? 'zzz'
                        ),
                        ['level_bawahan', 'asc'],
                        ['nm_lengkap', 'asc'],
                    ])
                    ->values();
            }
        }

        // Group per departemen yang dibawahi (urutan grup mengikuti hasil sortBy).
        $bawahanPerDept = $bawahan->groupBy(fn ($k) => $k->departemen->nm_dept ?? 'Tanpa Departemen');

        return view('profile.index', compact('user', 'karyawan', 'bawahan', 'bawahanPerDept'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        // TODO: Implement profile update logic if needed
        return back()->with('success', 'Profile updated successfully.');
    }
}
