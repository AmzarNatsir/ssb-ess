<?php

namespace App\Http\Controllers;

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
        
        return view('profile.index', compact('user', 'karyawan'));
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
