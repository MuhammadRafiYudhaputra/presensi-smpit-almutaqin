<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Siswa;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PortalGuruController extends Controller
{
    /**
     * Get the logged-in Guru's assigned Class
     */
    private function getGuruKelas()
    {
        $user = Auth::user();
        if (!$user) return null;

        $guru = $user->guru;
        if (!$guru) return null;

        return $guru->kelas;
    }

    private function calculateDefaultHariEfektif($mode, $bulan, $tahun, $semester, $kelasNama = null)
    {
        if ($mode === 'bulanan') {
            $startDate = Carbon::createFromDate($tahun, $bulan, 1);
            $endDate = $startDate->copy()->endOfMonth();
            $weekdays = 0;
            $current = $startDate->copy();
            while ($current <= $endDate) {
                if (!$current->isWeekend()) {
                    $weekdays++;
                }
                $current->addDay();
            }
            return max(1, $weekdays);
        } elseif ($mode === 'semester') {
            if ($kelasNama && str_contains(strtoupper($kelasNama), '9') && $semester === 'genap') {
                return 90;
            }

            $startMonth = ($semester === 'ganjil') ? 7 : 1;
            $endMonth = ($semester === 'ganjil') ? 12 : 6;
            $startDate = Carbon::createFromDate($tahun, $startMonth, 1);
            $endDate = Carbon::createFromDate($tahun, $endMonth, 1)->endOfMonth();
            $weekdays = 0;
            $current = $startDate->copy();
            while ($current <= $endDate) {
                if (!$current->isWeekend()) {
                    $weekdays++;
                }
                $current->addDay();
            }
            return max(1, $weekdays);
        }
        return 1;
    }

    public function monitoring(Request $request)
    {
        $kelas = $this->getGuruKelas();
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        $query = Kehadiran::with(['siswa.kelas'])
            ->whereDate('tanggal', $tanggal);

        if ($kelas) {
            $query->whereHas('siswa', function ($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
            });
        }

        $kehadirans = $query->latest('jam_masuk')->paginate(20)->withQueryString();

        return view('guru.monitoring', compact('kehadirans', 'tanggal', 'kelas'));
    }

    public function rekap(Request $request)
    {
        $kelas = $this->getGuruKelas();
        $mode = $request->get('mode', 'harian');
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $bulan = (int)$request->get('bulan', date('n'));
        $tahun = (int)$request->get('tahun', date('Y'));
        $semester = $request->get('semester', (date('n') >= 7 ? 'ganjil' : 'genap'));
        $sortBy = $request->get('sort_by', 'nama_asc');

        $defaultHariEfektif = $this->calculateDefaultHariEfektif($mode, $bulan, $tahun, $semester, $kelas ? $kelas->nama_kelas : null);
        $hariEfektif = (int) $request->get('hari_efektif', $defaultHariEfektif);
        if ($hariEfektif <= 0) $hariEfektif = $defaultHariEfektif;

        // Query Siswa for this class
        $siswaQuery = Siswa::with('kelas')
            ->where(function ($q) {
                $q->where('status', '!=', 'alumni')->orWhereNull('status');
            });

        if ($kelas) {
            $siswaQuery->where('kelas_id', $kelas->id);
        }

        switch ($sortBy) {
            case 'nama_desc':
                $siswaQuery->orderBy('nama', 'desc');
                break;
            case 'nisn':
                $siswaQuery->orderBy('nisn', 'asc');
                break;
            case 'nama_asc':
            default:
                $siswaQuery->orderBy('nama', 'asc');
                break;
        }

        $siswas = $siswaQuery->get();

        $harianData = [];
        $bulananData = [];
        $semesterData = [];

        if ($mode === 'harian') {
            foreach ($siswas as $s) {
                $kh = Kehadiran::where('siswa_id', $s->id)
                    ->whereDate('tanggal', $tanggal)
                    ->first();

                $harianData[] = (object)[
                    'siswa' => $s,
                    'jam_masuk' => $kh ? $kh->jam_masuk : null,
                    'jam_pulang' => $kh ? $kh->jam_pulang : null,
                    'status' => $kh ? $kh->status : 'BELUM ABSEN',
                ];
            }
        } elseif ($mode === 'bulanan') {
            foreach ($siswas as $s) {
                $tepatWaktu = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'HADIR')
                    ->count();

                $terlambat = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'TERLAMBAT')
                    ->count();

                $izin = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'IZIN')
                    ->count();

                $sakit = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'SAKIT')
                    ->count();

                $alpa = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'ALPA')
                    ->count();

                $totalHadir = $tepatWaktu + $terlambat;
                $persentase = ($hariEfektif > 0) ? round(($totalHadir / $hariEfektif) * 100, 1) : 0;

                $bulananData[] = (object)[
                    'siswa' => $s,
                    'hadir' => $totalHadir,
                    'terlambat' => $terlambat,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                    'persentase' => min(100, $persentase),
                ];
            }
        } elseif ($mode === 'semester') {
            $startMonth = ($semester === 'ganjil') ? 7 : 1;
            $endMonth = ($semester === 'ganjil') ? 12 : 6;

            foreach ($siswas as $s) {
                $tepatWaktu = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) AS INTEGER)'), [$startMonth, $endMonth])
                    ->where('status', 'HADIR')
                    ->count();

                $terlambat = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) AS INTEGER)'), [$startMonth, $endMonth])
                    ->where('status', 'TERLAMBAT')
                    ->count();

                $izin = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) AS INTEGER)'), [$startMonth, $endMonth])
                    ->where('status', 'IZIN')
                    ->count();

                $sakit = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) AS INTEGER)'), [$startMonth, $endMonth])
                    ->where('status', 'SAKIT')
                    ->count();

                $alpa = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) AS INTEGER)'), [$startMonth, $endMonth])
                    ->where('status', 'ALPA')
                    ->count();

                $totalHadir = $tepatWaktu + $terlambat;
                $persentase = ($hariEfektif > 0) ? round(($totalHadir / $hariEfektif) * 100, 1) : 0;

                $semesterData[] = (object)[
                    'siswa' => $s,
                    'hadir' => $totalHadir,
                    'terlambat' => $terlambat,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                    'persentase' => min(100, $persentase),
                ];
            }
        }

        return view('guru.rekap', compact(
            'kelas',
            'mode',
            'tanggal',
            'bulan',
            'tahun',
            'semester',
            'sortBy',
            'hariEfektif',
            'harianData',
            'bulananData',
            'semesterData'
        ));
    }
}
