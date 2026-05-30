<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    /**
     * Proxy request to external media application.
     */
    public function proxy(Request $request)
    {
        $path = $request->query('path');
        $type = $request->query('type', 'memo'); // see $endpoints below

        if (!$path) {
            abort(404);
        }

        $baseUrl = config('services.media.url');
        $token = config('services.media.token');

        // Map the request type to the corresponding endpoint on the HRD app.
        // 'photo'/'memo' use the media API (path = filename); the recruitment/
        // employee endpoints use the HRD routes (path = record id).
        $endpoints = [
            'photo'           => '/api/media/photo/',
            'memo'            => '/api/media/memo-internal/',
            'pelamar_photo'   => '/hrd/recruitment/photo/',
            'pelamar_dokumen' => '/hrd/employee/dokument/',
        ];
        $subPath = $endpoints[$type] ?? $endpoints['memo'];
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
}
