<?php

namespace App\Http\Controllers;

use App\Models\Karyawan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    private function buildInitialsAvatar(string $name = '')
    {
        $name = trim($name);
        $initials = '?';

        if ($name !== '') {
            $words = preg_split('/\s+/', $name);
            $initials = '';
            foreach ($words as $word) {
                if ($word !== '') {
                    $initials .= strtoupper(substr($word, 0, 1));
                }
                if (strlen($initials) >= 2) {
                    break;
                }
            }
            if ($initials === '') {
                $initials = '?';
            }
        }

        $svg = '<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 160 160">'
            . '<defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1">'
            . '<stop offset="0%" stop-color="#ff6b35"/><stop offset="100%" stop-color="#f7931e"/>'
            . '</linearGradient></defs>'
            . '<rect width="160" height="160" rx="80" fill="url(#g)"/>'
            . '<text x="50%" y="54%" dominant-baseline="middle" text-anchor="middle" '
            . 'font-family="Arial, sans-serif" font-size="56" font-weight="700" fill="#ffffff">'
            . e($initials)
            . '</text></svg>';

        return response($svg, 200)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Cache-Control', 'public, max-age=86400');
    }

    private function sendProxyRequest(string $subPath, string $path)
    {
        if (!$path) {
            abort(404);
        }

        $baseUrl = config('services.media.url');
        $token = config('services.media.token');
        $url = rtrim($baseUrl, '/') . $subPath . $path;

        $status = null;
        try {
            $response = Http::withToken($token)
                ->withoutVerifying()
                ->get($url);

            if ($response->successful()) {
                return response($response->body(), 200)
                    ->header('Content-Type', $response->header('Content-Type'))
                    ->header('Content-Disposition', $response->header('Content-Disposition'));
            }

            $status = $response->status();
            Log::error("Media proxy failed for path: {$path}. Status: " . $status, [
                'url' => $url,
                'response' => $response->json() ?: $response->body()
            ]);
        } catch (\Exception $e) {
            Log::error("Media proxy error: " . $e->getMessage(), [
                'url' => $url,
                'path' => $path
            ]);
            abort(500);
        }

        if ($status) {
            abort($status);
        }
    }

    /**
     * Proxy request to external media application.
     */
    public function proxy(Request $request)
    {
        $path = $request->query('path');
        $type = $request->query('type', 'memo'); // see $endpoints below

        // Map the request type to the corresponding endpoint on the HRD app.
        // 'photo'/'memo' use the media API (path = filename); the recruitment/
        // employee endpoints use the HRD routes (path = record id).
        $endpoints = [
            'photo'           => '/api/media/photo/',
            'memo'            => '/api/media/memo-internal/',
            'pelamar_photo'   => '/hrd/recruitment/photo/',
            'pelamar_dokumen' => '/hrd/employee/dokument/',
            'karyawan_photo'  => '/hrd/photo/',
            'hasil_evaluasi'  => '/hrd/hasil-evaluasi/',
        ];
        $subPath = $endpoints[$type] ?? $endpoints['memo'];
        return $this->sendProxyRequest($subPath, $path);
    }

    public function photo($id_karyawan)
    {
        $karyawan = Karyawan::find($id_karyawan);
        $name = $karyawan?->nm_lengkap ?? '';
        $photoFile = $karyawan?->photo;

        if (empty($photoFile)) {
            return $this->buildInitialsAvatar($name);
        }

        $baseUrl = config('services.media.url');
        $token = config('services.media.token');
        $url = rtrim($baseUrl, '/') . '/api/media/photo/' . $photoFile;

        try {
            $response = Http::withToken($token)
                ->withoutVerifying()
                ->get($url);

            if ($response->successful()) {
                return response($response->body(), 200)
                    ->header('Content-Type', $response->header('Content-Type'))
                    ->header('Content-Disposition', $response->header('Content-Disposition'));
            }

            Log::warning("Photo not found on media service, using initials avatar.", [
                'id_karyawan' => $id_karyawan,
                'photo_file' => $photoFile,
                'url' => $url,
                'status' => $response->status(),
            ]);
        } catch (\Exception $e) {
            Log::warning("Photo proxy error, using initials avatar.", [
                'id_karyawan' => $id_karyawan,
                'photo_file' => $photoFile,
                'url' => $url,
                'error' => $e->getMessage(),
            ]);
        }

        return $this->buildInitialsAvatar($name);
    }

    public function hasil_evaluasi($id)
    {
        return $this->sendProxyRequest('/hrd/hasil-evaluasi/', (string) $id);
    }
}
