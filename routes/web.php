<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\OrangTuaController;
use App\Http\Controllers\Admin\FonnteSettingController;
use App\Http\Controllers\Admin\RekapKehadiranController;
use App\Http\Controllers\Admin\JamPresensiController;
use App\Http\Controllers\Admin\KenaikanKelasController;
use App\Http\Controllers\Admin\SettingAkademikController;
use App\Http\Controllers\Presensi\ScanPresensiController;
use App\Http\Controllers\Guru\PortalGuruController;

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot.password');

// Root Route Redirect
Route::get('/', function () {
    if (Auth::check()) {
        if (Auth::user()->role === 'guru') {
            return redirect()->route('guru.monitoring');
        }
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Endpoint Public Kios Presensi QR Code Scanner (Bisa diakses langsung oleh alat scanner)
Route::get('/scan', [ScanPresensiController::class, 'index'])->name('presensi.scan');
Route::post('/scan/process', [ScanPresensiController::class, 'store'])->name('presensi.scan.store');

// Guru (Wali Kelas) Routes Group (Wajib Login)
Route::middleware(['auth'])->prefix('guru')->name('guru.')->group(function () {
    Route::get('/monitoring', [PortalGuruController::class, 'monitoring'])->name('monitoring');
    Route::get('/rekap', [PortalGuruController::class, 'rekap'])->name('rekap');
    Route::get('/siswa', [PortalGuruController::class, 'siswa'])->name('siswa.index');
});

// Admin Protected Routes Group (Wajib Login)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Data Siswa & Generate QR Code Kartu
    Route::resource('/siswa', SiswaController::class)->except(['create', 'edit']);
    Route::get('/siswa/{id}/card', [SiswaController::class, 'printCard'])->name('siswa.card');

    // Data Guru & Reset Password 1-Klik
    Route::resource('/guru', GuruController::class)->except(['create', 'edit']);
    Route::post('/guru/{id}/reset-password', [GuruController::class, 'resetPassword'])->name('guru.resetPassword');

    // Data Kelas & Kenaikan Kelas Otomatis
    Route::resource('/kelas', KelasController::class)->except(['create', 'edit']);
    Route::get('/kenaikan-kelas', [KenaikanKelasController::class, 'index'])->name('kenaikan.index');
    Route::post('/kenaikan-kelas', [KenaikanKelasController::class, 'proses'])->name('kenaikan.proses');
    Route::post('/kenaikan-kelas/pindah', [KenaikanKelasController::class, 'pindahRombel'])->name('kenaikan.pindah');

    // Data Orang Tua
    Route::resource('/orangtua', OrangTuaController::class)->except(['create', 'edit']);

    // Jam Operasional Presensi
    Route::get('/jampresensi', [JamPresensiController::class, 'index'])->name('jampresensi.index');
    Route::post('/jampresensi', [JamPresensiController::class, 'update'])->name('jampresensi.update');

    // Setting Fonnte WhatsApp API
    Route::get('/fonnte', [FonnteSettingController::class, 'index'])->name('fonnte.index');
    Route::post('/fonnte', [FonnteSettingController::class, 'update'])->name('fonnte.update');
    Route::post('/fonnte/test', [FonnteSettingController::class, 'testSend'])->name('fonnte.test');

    // Monitoring & Rekap Laporan
    Route::get('/monitoring', [RekapKehadiranController::class, 'monitoring'])->name('rekap.monitoring');
    Route::get('/rekap', [RekapKehadiranController::class, 'rekap'])->name('rekap.index');
    Route::post('/rekap/update-status', [RekapKehadiranController::class, 'updateStatus'])->name('rekap.updateStatus');
    Route::get('/laporan', [RekapKehadiranController::class, 'generateLaporanIndex'])->name('laporan.index');
    Route::get('/rekap/cetak', [RekapKehadiranController::class, 'cetakLaporan'])->name('rekap.cetak');

    // Setting Semester & Tahun Ajaran Aktif
    Route::post('/setting-akademik', [SettingAkademikController::class, 'update'])->name('setting.akademik.update');
    Route::post('/setting-akademik/toggle', [SettingAkademikController::class, 'toggleSemester'])->name('setting.akademik.toggle');

    // Backup & Restore Database & Storage Asset
    Route::get('/backup', [App\Http\Controllers\Admin\BackupController::class, 'index'])->name('backup.index');
    Route::get('/backup/database', [App\Http\Controllers\Admin\BackupController::class, 'backupDatabase'])->name('backup.database');
    Route::post('/backup/database/restore', [App\Http\Controllers\Admin\BackupController::class, 'restoreDatabase'])->name('backup.database.restore');
    Route::get('/backup/storage', [App\Http\Controllers\Admin\BackupController::class, 'backupStorage'])->name('backup.storage');
    Route::post('/backup/storage/restore', [App\Http\Controllers\Admin\BackupController::class, 'restoreStorage'])->name('backup.storage.restore');
});
