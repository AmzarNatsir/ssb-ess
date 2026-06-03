<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DocumentController;

/*
|--------------------------------------------------------------------------
| API Routes - Document Serving
|--------------------------------------------------------------------------
|
| Semua route di sini dilindungi oleh middleware ApiTokenAuth.
| Sertakan header: Authorization: Bearer <token>
|
*/

Route::middleware(\App\Http\Middleware\ApiTokenAuth::class)->group(function () {

    // Metadata file — didaftarkan LEBIH DULU agar tidak tertangkap route serve
    Route::get('/documents/{type}/{filename}/info', [DocumentController::class, 'info'])
        ->name('api.documents.info')
        ->where('type', '[a-z]+')
        ->where('filename', '[^/]+');

    // Stream / tampilkan file
    Route::get('/documents/{type}/{filename}', [DocumentController::class, 'serve'])
        ->name('api.documents.serve')
        ->where('type', '[a-z]+')
        ->where('filename', '[^/]+');
});
