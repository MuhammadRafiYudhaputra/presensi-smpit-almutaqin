<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Siswa;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\Kehadiran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $today = Carbon::today('Asia/Jakarta')->toDateString();
        $selectedKelasId = $request->get('kelas_id');

        // 1. Master Counts
        $totalSiswa = Siswa::whereNotNull('kelas_id')->where(function ($q) {
            $q->where('status', '!=', 'alumni')->orWhereNull('status');
        })->count();
        $totalAlumni = Siswa::where('status', 'alumni')->orWhereNull('kelas_id')->count();
        $totalGuru = Guru::count();
        $totalKelas = Kelas::count();
        $kelases = Kelas::withCount(['siswas' => function($q) {
            $q->whereNotNull('kelas_id')->where(function ($q2) {
                $q2->where('status', '!=', 'alumni')->orWhereNull('status');
            });
        }])->get();

        // 2. Kehadiran Hari Ini (Filterable by Class if selected)
        $presensiHariIniQuery = Kehadiran::with(['siswa.kelas'])
            ->where('tanggal', $today)
            ->whereHas('siswa', function ($q) {
                $q->whereNotNull('kelas_id')->where(function ($q2) {
                    $q2->where('status', '!=', 'alumni')->orWhereNull('status');
                });
            });

        $totalSiswaFiltered = $totalSiswa;
        if ($selectedKelasId) {
            $presensiHariIniQuery->whereHas('siswa', function($q) use ($selectedKelasId) {
                $q->where('kelas_id', $selectedKelasId);
            });
            $selectedKelas = Kelas::find($selectedKelasId);
            $totalSiswaFiltered = $selectedKelas ? Siswa::where('kelas_id', $selectedKelasId)->whereNotNull('kelas_id')->where(function($q) { $q->where('status', '!=', 'alumni')->orWhereNull('status'); })->count() : $totalSiswa;
        }

        $presensiHariIni = $presensiHariIniQuery->get();

        $totalHadir = $presensiHariIni->where('status', 'HADIR')->count();
        $totalTerlambat = $presensiHariIni->where('status', 'TERLAMBAT')->count();
        $totalSakit = $presensiHariIni->where('status', 'SAKIT')->count();
        $totalIzin = $presensiHariIni->where('status', 'IZIN')->count();
        
        $totalMasuk = $totalHadir + $totalTerlambat;
        $totalPresensi = $totalMasuk + $totalSakit + $totalIzin;

        $isTodayHoliday = \App\Helpers\HolidayHelper::isNonEffectiveDay($today);
        if ($isTodayHoliday || $totalPresensi === 0) {
            $totalAlpa = 0;
        } else {
            $totalAlpa = max(0, $totalSiswaFiltered - $totalPresensi);
        }

        // 3. 7 Days Trend for Chart.js
        $chartLabels = [];
        $chartHadir = [];
        $chartSakit = [];
        $chartIzin = [];
        $chartAlfa = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today('Asia/Jakarta')->subDays($i);
            $dateString = $date->toDateString();
            $label = ($i === 0) ? 'Hari Ini' : $date->translatedFormat('d M');
            $chartLabels[] = $label;

            $dayKhs = Kehadiran::where('tanggal', $dateString)
                ->whereHas('siswa', function ($q) {
                    $q->whereNotNull('kelas_id')->where(function ($q2) {
                        $q2->where('status', '!=', 'alumni')->orWhereNull('status');
                    });
                })->get();

            $h = $dayKhs->whereIn('status', ['HADIR', 'TERLAMBAT'])->count();
            $s = $dayKhs->where('status', 'SAKIT')->count();
            $iz = $dayKhs->where('status', 'IZIN')->count();
            $tot = $h + $s + $iz;

            $isNonEffective = \App\Helpers\HolidayHelper::isNonEffectiveDay($date);
            // Jika tanggal merah / libur nasional / weekend, ATAU belum ada kehadiran sama sekali, maka alpa = 0
            if ($isNonEffective || $tot === 0) {
                $a = 0;
            } else {
                $a = max(0, $totalSiswa - $tot);
            }

            $chartHadir[] = $h;
            $chartSakit[] = $s;
            $chartIzin[] = $iz;
            $chartAlfa[] = $a;
        }

        // 4. Recent Activity
        $recentPresensi = Kehadiran::with(['siswa.kelas'])
            ->where('tanggal', $today)
            ->orderBy('updated_at', 'desc')
            ->limit(8)
            ->get();

        return view('admin.dashboard', compact(
            'totalSiswa',
            'totalAlumni',
            'totalGuru',
            'totalKelas',
            'kelases',
            'selectedKelasId',
            'totalSiswaFiltered',
            'totalHadir',
            'totalTerlambat',
            'totalSakit',
            'totalIzin',
            'totalAlpa',
            'totalMasuk',
            'totalPresensi',
            'chartLabels',
            'chartHadir',
            'chartSakit',
            'chartIzin',
            'chartAlfa',
            'recentPresensi'
        ));
    }
}
