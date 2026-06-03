<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentController extends Controller
{
    /**
     * Mapping: document type => storage disk 'public' folder.
     */
    private const FOLDER_MAP = [
        'pinjaman'  => 'pinjaman_dokumen',
        'training'  => 'training_evidence',
        'overtime'  => 'overtime_orders',
        'perdis'    => 'perdis_documents',
        'resign'    => 'resign_dokumen',
    ];

    /**
     * GET /api/documents/{type}/{filename}
     *
     * Stream file dari storage/app/public/{folder}/{filename}.
     * Membutuhkan header: Authorization: Bearer <token>
     *
     * @param  string  $type     salah satu: pinjaman|training|overtime|perdis|resign
     * @param  string  $filename nama file (tanpa path)
     */
    public function serve(Request $request, string $type, string $filename): StreamedResponse|\Illuminate\Http\JsonResponse
    {
        if (!array_key_exists($type, self::FOLDER_MAP)) {
            return response()->json([
                'success' => false,
                'message' => "Tipe dokumen '$type' tidak dikenal. Gunakan: " . implode(', ', array_keys(self::FOLDER_MAP)),
            ], 404);
        }

        // Sanitasi: pastikan filename tidak mengandung path traversal
        $filename = basename($filename);

        $relativePath = self::FOLDER_MAP[$type] . '/' . $filename;

        if (!Storage::disk('public')->exists($relativePath)) {
            return response()->json([
                'success' => false,
                'message' => 'File tidak ditemukan.',
            ], 404);
        }

        $fullPath  = Storage::disk('public')->path($relativePath);
        $mimeType  = mime_content_type($fullPath) ?: 'application/octet-stream';
        $size     = Storage::disk('public')->size($relativePath);

        return response()->stream(function () use ($relativePath): void {
            $stream = Storage::disk('public')->readStream($relativePath);
            fpassthru($stream);
            if (is_resource($stream)) {
                fclose($stream);
            }
        }, 200, [
            'Content-Type'        => $mimeType,
            'Content-Length'      => $size,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
            'Cache-Control'       => 'private, max-age=3600',
        ]);
    }

    /**
     * GET /api/documents/{type}/{filename}/info
     *
     * Kembalikan metadata file (ukuran, mime, last modified) tanpa stream konten.
     */
    public function info(Request $request, string $type, string $filename): \Illuminate\Http\JsonResponse
    {
        if (!array_key_exists($type, self::FOLDER_MAP)) {
            return response()->json([
                'success' => false,
                'message' => "Tipe dokumen '$type' tidak dikenal.",
            ], 404);
        }

        $filename     = basename($filename);
        $relativePath = self::FOLDER_MAP[$type] . '/' . $filename;

        if (!Storage::disk('public')->exists($relativePath)) {
            return response()->json(['success' => false, 'message' => 'File tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => [
                'filename'      => $filename,
                'type'          => $type,
                'folder'        => self::FOLDER_MAP[$type],
                'mime_type'     => mime_content_type(Storage::disk('public')->path($relativePath)) ?: null,
                'size_bytes'    => Storage::disk('public')->size($relativePath),
                'last_modified' => date('Y-m-d H:i:s', Storage::disk('public')->lastModified($relativePath)),
                'url'           => Storage::url($relativePath),
            ],
        ]);
    }
}
