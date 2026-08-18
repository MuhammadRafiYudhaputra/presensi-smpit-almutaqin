<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\OrangTuaController;
use App\Http\Controllers\Admin\FonnteSettingController;
use App\Http\Controllers\Admin\RekapKehadiranController;
use App\Http\Controllers\Presensi\ScanPresensiController;

// Redirect Root to Dashboard
Route::get('/', function () {
    return redirect()->route('admin.dashboard');
});

// Endpoint Public Kios Presensi QR Code Scanner
Route::get('/scan', [ScanPresensiController::class, 'index'])->name('presensi.scan');
Route::post('/scan/process', [ScanPresensiController::class, 'store'])->name('presensi.scan.store');

// Admin Routes Group
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Siswa & Generate QR Code Kartu
    Route::resource('/siswa', SiswaController::class)->except(['create', 'edit']);
    Route::get('/siswa/{id}/card', [SiswaController::class, 'printCard'])->name('siswa.card');

    // Data Guru
    Route::resource('/guru', GuruController::class)->except(['create', 'edit', 'update']);

    // Data Kelas
    Route::resource('/kelas', KelasController::class)->except(['create', 'edit', 'update']);

    // Data Orang Tua
    Route::resource('/orangtua', OrangTuaController::class)->except(['create', 'edit', 'update']);

    // Setting Fonnte WhatsApp API
    Route::get('/fonnte', [FonnteSettingController::class, 'index'])->name('fonnte.index');
    Route::post('/fonnte', [FonnteSettingController::class, 'update'])->name('fonnte.update');
    Route::post('/fonnte/test', [FonnteSettingController::class, 'testSend'])->name('fonnte.test');

    // Monitoring & Rekap Laporan
    Route::get('/monitoring', [RekapKehadiranController::class, 'monitoring'])->name('rekap.monitoring');
    Route::get('/rekap', [RekapKehadiranController::class, 'rekap'])->name('rekap.index');
    Route::get('/rekap/cetak', [RekapKehadiranController::class, 'cetakLaporan'])->name('rekap.cetak');
});
