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
        $type = $request->query('type', 'memo'); // 'memo' or 'photo'

        if (!$path) {
            abort(404);
        }

        $baseUrl = config('services.media.url');
        $token = config('services.media.token');
        
        // Determine subpath based on type
        // Based on media app routes: /api/media/photo/{filename} or /api/media/memo-internal/{filename}
        $subPath = $type === 'photo' ? 'photo' : 'memo-internal';
        $endpoint = rtrim($baseUrl, '/') . '/api/media/' . $subPath . '/';
        $url = $endpoint . $path;

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
