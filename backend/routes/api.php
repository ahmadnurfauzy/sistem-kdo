<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Sistem KDO — versi 1
|--------------------------------------------------------------------------
| Semua endpoint berada di bawah prefix /api/v1 (prefix "api" otomatis
| dari bootstrap/app.php). Autentikasi memakai bearer token Sanctum.
*/

Route::prefix('v1')->group(function (): void {

    // ---------- AUTH ----------
    // login dibatasi rate limit lebih ketat untuk mencegah brute force
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1'); // maks 10 percobaan login per menit

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // ---------- RESOURCE (Fase 4) ----------
        // kendaraan, pegawai, perangkat-daerah, laporan — menyusul
    });
});
