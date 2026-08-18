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
        $today = Carbon::today()->toDateString();

        $totalSiswa = Siswa::count();
        $totalGuru = Guru::count();
        $totalKelas = Kelas::count();

        $presensiHariIni = Kehadiran::where('tanggal', $today)->get();

        $totalHadir = $presensiHariIni->where('status', 'HADIR')->count();
        $totalTerlambat = $presensiHariIni->where('status', 'TERLAMBAT')->count();
        $totalIzinSakit = $presensiHariIni->whereIn('status', ['IZIN', 'SAKIT'])->count();
        $totalAlpa = $totalSiswa - ($totalHadir + $totalTerlambat + $totalIzinSakit);

        $recentPresensi = Kehadiran::with(['siswa.kelas'])
            ->where('tanggal', $today)
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact(
            'totalSiswa',
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
