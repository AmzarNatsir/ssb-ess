<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Login lokal NIK+password dinonaktifkan — autentikasi dilakukan via SSO SSB
     * (lihat App\Http\Controllers\Auth\SsbSsoController). Method showLoginForm()
     * dan login() sengaja dihapus agar tidak ada jalur bypass SSO.
     */

    /**
     * Log the user out of the application.
     *
     * Single Logout: setelah membersihkan session lokal, arahkan browser ke
     * endpoint SLO IdP (GET /sso/logout) agar session SSB ikut berakhir + token
     * di-revoke, lalu IdP redirect balik ke halaman login app ini. Dengan begitu
     * "Masuk dengan SSO" berikutnya selalu minta NIK lagi (bisa ganti user).
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        $base = rtrim((string) config('services.ssb.base_url'), '/');
        $return = route('login') . '?logout=1';
        $ssoLogout = $base . '/sso/logout?redirect=' . urlencode($return);

        return redirect()->away($ssoLogout);
    }

    /**
     * Show the password change form.
     */
    public function showChangePasswordForm()
    {
        return view('auth.change-password');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'password' => [
                'required',
                'string',
                'confirmed',
                \Illuminate\Validation\Rules\Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
            ],
        ]);

        $user = Auth::user();
        $user->password = \Illuminate\Support\Facades\Hash::make($request->password);
        $user->must_change_password = 0;
        $user->save();

        return redirect()->route('home')->with('success', 'Your password has been changed successfully.');
    }
}

