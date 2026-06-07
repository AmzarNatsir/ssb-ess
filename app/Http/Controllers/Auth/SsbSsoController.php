<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * SSO client (Employee/ESS) ke Identity Provider SSB.
 * Alur: OAuth2 Authorization Code + PKCE.
 *
 * Catatan: app ini memakai DB yang SAMA dengan IdP (ssb_2025), sehingga tabel
 * `users` di-share. "find-or-create by NIK" diganti lookup saja (user pasti ada),
 * dan TIDAK menulis kolom name/email karena tabel `users` tidak memilikinya
 * (nama diambil dari relasi karyawan -> hrd_karyawan).
 */
class SsbSsoController extends Controller
{
    /**
     * Bangun URL absolut ke endpoint SSB, aman dari trailing slash pada
     * SSB_BASE_URL (mencegah "//oauth/authorize" yang bisa 404 di server).
     */
    protected function ssbUrl(string $path): string
    {
        return rtrim((string) config('services.ssb.base_url'), '/') . '/' . ltrim($path, '/');
    }

    /**
     * Mulai login: arahkan ke SSB /oauth/authorize dengan PKCE + state.
     */
    public function redirectToProvider(Request $request)
    {
        $verifier  = Str::random(64);
        $state     = Str::random(40);
        $challenge = rtrim(strtr(base64_encode(hash('sha256', $verifier, true)), '+/', '-_'), '=');

        $request->session()->put('ssb_code_verifier', $verifier);
        $request->session()->put('ssb_state', $state);

        $query = http_build_query([
            'client_id'             => config('services.ssb.client_id'),
            'redirect_uri'          => config('services.ssb.redirect'),
            'response_type'         => 'code',
            'scope'                 => '',
            'state'                 => $state,
            'code_challenge'        => $challenge,
            'code_challenge_method' => 'S256',
        ]);

        return redirect($this->ssbUrl('/oauth/authorize') . '?' . $query);
    }

    /**
     * Callback: tukar code -> token, ambil identitas, login user lokal.
     */
    public function handleCallback(Request $request)
    {
        // 1) Validasi state (anti-CSRF)
        $expectedState = $request->session()->pull('ssb_state');
        if (! $expectedState || ! hash_equals($expectedState, (string) $request->input('state'))) {
            Log::warning('SSO state tidak valid', [
                'reason'        => $expectedState ? 'mismatch' : 'session_kosong',
                'has_expected'  => (bool) $expectedState,
                'session_id'    => $request->session()->getId(),
                'has_cookie'    => $request->hasCookie(config('session.cookie')),
            ]);
            abort(403, 'State tidak valid.');
        }

        if ($request->filled('error')) {
            abort(403, 'Otorisasi ditolak: ' . $request->input('error'));
        }

        $verifier = $request->session()->pull('ssb_code_verifier');

        // 2) Tukar authorization code -> access token
        $payload = [
            'grant_type'    => 'authorization_code',
            'client_id'     => config('services.ssb.client_id'),
            'redirect_uri'  => config('services.ssb.redirect'),
            'code_verifier' => $verifier,
            'code'          => $request->input('code'),
        ];
        // Client confidential: sertakan secret. Public/PKCE: biarkan kosong.
        if ($secret = config('services.ssb.client_secret')) {
            $payload['client_secret'] = $secret;
        }

        $tokenResp = Http::asForm()->post($this->ssbUrl('/oauth/token'), $payload);

        if ($tokenResp->failed()) {
            Log::warning('SSO token exchange gagal', [
                'status'   => $tokenResp->status(),
                'body'     => $tokenResp->body(),
                'endpoint' => $this->ssbUrl('/oauth/token'),
                'client_id'=> config('services.ssb.client_id'),
                'redirect' => config('services.ssb.redirect'),
            ]);
            abort(401, 'Gagal menukar token: ' . $tokenResp->body());
        }

        $tokens      = $tokenResp->json();
        $accessToken = $tokens['access_token'] ?? null;
        if (! $accessToken) {
            abort(401, 'Access token kosong.');
        }

        // 3) Ambil identitas (tanpa role) dari SSB
        $userResp = Http::withToken($accessToken)
            ->acceptJson()
            ->get($this->ssbUrl('/api/oauth/userinfo'));
        if ($userResp->status() === 403) {
            abort(403, 'Anda tidak memiliki akses ke aplikasi ini. Hubungi administrator.');
        }
        if ($userResp->failed()) {
            Log::warning('SSO userinfo gagal', [
                'status'   => $userResp->status(),
                'body'     => $userResp->body(),
                'endpoint' => $this->ssbUrl('/api/oauth/userinfo'),
            ]);
            abort(401, 'Gagal mengambil userinfo dari SSB.');
        }

        $info = $userResp->json();

        // Tolak karyawan nonaktif
        if (isset($info['is_active']) && $info['is_active'] === false) {
            abort(403, 'Akun karyawan nonaktif.');
        }

        // 4) Lookup user lokal berdasar NIK.
        //    DB di-share dengan IdP -> user pasti sudah ada. Tidak menulis kolom
        //    name/email karena tabel `users` tidak memilikinya.
        $user = User::where('nik', $info['nik'])->first();
        if (! $user) {
            abort(403, 'NIK tidak terdaftar di sistem.');
        }

        // (opsional) simpan token untuk panggilan API ke SSB berikutnya
        $request->session()->put('ssb_tokens', [
            'access_token'  => $accessToken,
            'refresh_token' => $tokens['refresh_token'] ?? null,
            'expires_in'    => $tokens['expires_in'] ?? null,
        ]);

        // 5) Tetapkan ROLE LOKAL (kebijakan app ini, bukan dari SSB)
        $this->mapLocalRole($user, $info);

        // 6) Login & masuk aplikasi
        Auth::login($user, true);
        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    /**
     * Masuk sebagai pengguna lain (force re-auth) TANPA logout manual.
     *
     * Tetap mempertahankan sesi ESS saat ini (tidak Auth::logout di sini), lalu
     * arahkan ke IdP /sso/logout dengan redirect balik ke awal alur authorize.
     * IdP akhiri sesi-nya → saat authorize ulang, form login SSB muncul. Setelah
     * login NIK baru, callback me-login-kan user tsb (mengganti sesi ESS). Jika
     * user batal di form login, sesi ESS lama tetap berlaku.
     */
    public function switchUser()
    {
        $base = rtrim((string) config('services.ssb.base_url'), '/');

        return redirect()->away($base . '/sso/logout?redirect=' . urlencode(route('ssb.redirect')));
    }

    /**
     * Logout lokal. (Single Logout terpusat = Tahap 5 di IdP.)
     *
     * Selama session IdP masih hidup, akses ulang akan otomatis re-login lewat SSO.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }

    /**
     * Kebijakan role LOKAL app ini.
     *
     * App employee/ESS saat ini belum memiliki sistem role lokal (tidak memakai
     * Spatie); login terbuka untuk semua user pada tabel `users`. Method dibiarkan
     * sebagai no-op. Isi di sini bila nanti dibutuhkan mapping NIK -> role lokal.
     */
    protected function mapLocalRole(User $user, array $info): void
    {
        // TODO: implementasi bila app ini sudah punya kebijakan role lokal.
    }
}
