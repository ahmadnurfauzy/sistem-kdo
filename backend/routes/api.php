<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\KendaraanController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\PegawaiController;
use App\Http\Controllers\Api\PerangkatDaerahController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| REST API Sistem KDO — versi 1
|--------------------------------------------------------------------------
| Semua endpoint berada di bawah prefix /api/v1. Autentikasi memakai
| bearer token Sanctum. Pemisahan akses (Gate bawaan Laravel):
|   - READ  (GET)  : semua pengguna terautentikasi (admin, pengelola, pengguna)
|   - WRITE data    : admin & pengelola  (middleware 'can:manage-data')
|   - Kelola akun   : hanya admin        (middleware 'can:manage-users')
*/

Route::prefix('v1')->group(function (): void {

    // ---------- AUTH ----------
    // login dibatasi rate limit lebih ketat untuk mencegah brute force
    Route::post('/auth/login', [AuthController::class, 'login'])
        ->middleware('throttle:10,1'); // maks 10 percobaan login per menit

    Route::middleware(['auth:sanctum', 'throttle:api'])->group(function (): void {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);

        // ---------- READ: semua pengguna terautentikasi ----------
        Route::get('/perangkat-daerah', [PerangkatDaerahController::class, 'index'])->name('perangkat-daerah.index');
        Route::get('/perangkat-daerah/{perangkat_daerah}', [PerangkatDaerahController::class, 'show'])->name('perangkat-daerah.show');

        Route::get('/pegawai', [PegawaiController::class, 'index'])->name('pegawai.index');
        Route::get('/pegawai/{pegawai}', [PegawaiController::class, 'show'])->name('pegawai.show');

        Route::get('/kendaraan', [KendaraanController::class, 'index'])->name('kendaraan.index');
        Route::get('/kendaraan/{kendaraan}', [KendaraanController::class, 'show'])->name('kendaraan.show');

        Route::get('/laporan/kondisi', [LaporanController::class, 'kondisi']);

        // ---------- WRITE: admin & pengelola ----------
        Route::middleware('can:manage-data')->group(function (): void {
            Route::post('/perangkat-daerah', [PerangkatDaerahController::class, 'store']);
            Route::put('/perangkat-daerah/{perangkat_daerah}', [PerangkatDaerahController::class, 'update']);
            Route::patch('/perangkat-daerah/{perangkat_daerah}', [PerangkatDaerahController::class, 'update']);
            Route::delete('/perangkat-daerah/{perangkat_daerah}', [PerangkatDaerahController::class, 'destroy']);

            Route::post('/pegawai', [PegawaiController::class, 'store']);
            Route::put('/pegawai/{pegawai}', [PegawaiController::class, 'update']);
            Route::patch('/pegawai/{pegawai}', [PegawaiController::class, 'update']);
            Route::delete('/pegawai/{pegawai}', [PegawaiController::class, 'destroy']);

            Route::post('/kendaraan', [KendaraanController::class, 'store']);
            Route::put('/kendaraan/{kendaraan}', [KendaraanController::class, 'update']);
            Route::patch('/kendaraan/{kendaraan}', [KendaraanController::class, 'update']);
            Route::delete('/kendaraan/{kendaraan}', [KendaraanController::class, 'destroy']);

            // endpoint khusus: tetapkan/ganti/lepas penanggung jawab kendaraan
            Route::put('/kendaraan/{kendaraan}/penanggung-jawab', [KendaraanController::class, 'assignPenanggungJawab']);
        });
    });
});
