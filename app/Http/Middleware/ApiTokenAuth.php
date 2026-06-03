<?php

namespace App\Http\Middleware;

use App\Models\ApiToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next): Response
    {
        $bearer = $request->bearerToken();

        if (empty($bearer)) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: token tidak ditemukan.',
            ], 401);
        }

        $tokenRecord = ApiToken::findByPlain($bearer);

        if (!$tokenRecord) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: token tidak valid.',
            ], 401);
        }

        if ($tokenRecord->isExpired()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: token sudah kadaluarsa.',
            ], 401);
        }

        // Update last used timestamp (non-blocking)
        $tokenRecord->last_used_at = now();
        $tokenRecord->saveQuietly();

        $request->attributes->set('api_token', $tokenRecord);

        return $next($request);
    }
}
