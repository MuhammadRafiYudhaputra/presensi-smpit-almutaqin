<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Kehadiran;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();

        // Hitung siswa aktif (bukan alumni)
        $totalSiswa = Siswa::where('status', '!=', 'alumni')->orWhereNull('status')->count();
        $totalAlumni = Siswa::where('status', 'alumni')->count();
        $totalGuru = Guru::count();
        $totalKelas = Kelas::count();

        // Kehadiran hari ini (hanya untuk siswa aktif)
        $presensiHariIni = Kehadiran::with('siswa')
            ->where('tanggal', $today)
            ->whereHas('siswa', function ($q) {
                $q->where('status', '!=', 'alumni')->orWhereNull('status');
            })
            ->get();

        $totalHadir = $presensiHariIni->where('status', 'HADIR')->count();
        $totalTerlambat = $presensiHariIni->where('status', 'TERLAMBAT')->count();
        $totalIzinSakit = $presensiHariIni->whereIn('status', ['IZIN', 'SAKIT'])->count();
        
        $totalPresensi = $totalHadir + $totalTerlambat + $totalIzinSakit;
        $totalAlpa = max(0, $totalSiswa - $totalPresensi);

        $recentPresensi = Kehadiran::with(['siswa.kelas'])
            ->where('tanggal', $today)
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalSiswa',
            'totalAlumni',
            'totalGuru',
            'totalKelas',
            'totalHadir',
            'totalTerlambat',
            'totalIzinSakit',
            'totalAlpa',
            'recentPresensi'
        ));
    }
}
