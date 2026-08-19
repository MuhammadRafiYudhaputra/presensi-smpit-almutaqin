<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Guru;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PortalGuruController extends Controller
{
    /**
     * Get the logged-in Guru's assigned Class (or selected class)
     */
    private function getGuruKelas(Request $request = null)
    {
        $user = Auth::user();
        if (!$user) return null;

        $guru = $user->guru ?: Guru::where('user_id', $user->id)->first();

        // 1. If explicit kelas_id requested in query
        if ($request && $request->filled('kelas_id')) {
            $requestedKelas = Kelas::find($request->get('kelas_id'));
            if ($requestedKelas) {
                return $requestedKelas;
            }
        }

        // 2. Class where this guru is wali kelas
        if ($guru) {
            $assignedKelas = Kelas::where('guru_id', $guru->id)->first();
            if ($assignedKelas) {
                return $assignedKelas;
            }
        }

        // 3. Fallback to first class in school if no specific class is assigned
        return Kelas::first();
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
        $kelas = $this->getGuruKelas($request);
        $allKelases = Kelas::all();
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        $query = Kehadiran::with(['siswa.kelas', 'siswa.orangTua'])
            ->whereDate('tanggal', $tanggal);

        if ($kelas) {
            $query->whereHas('siswa', function ($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id);
            });
        }

        $kehadirans = $query->latest('jam_masuk')->paginate(20)->withQueryString();

        return view('guru.monitoring', compact('kehadirans', 'tanggal', 'kelas', 'allKelases'));
    }

    public function rekap(Request $request)
    {
        $kelas = $this->getGuruKelas($request);
        $allKelases = Kelas::all();
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
            'allKelases',
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

    /**
     * Data Siswa Binaan Wali Kelas (Biodata, Kontak Orang Tua, Alamat)
     */
    public function siswa(Request $request)
    {
        $kelas = $this->getGuruKelas($request);
        $allKelases = Kelas::all();
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'nama_asc');

        $query = Siswa::with(['kelas', 'orangTua'])
            ->where(function ($q) {
                $q->where('status', '!=', 'alumni')->orWhereNull('status');
            });

        if ($kelas) {
            $query->where('kelas_id', $kelas->id);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%")
                  ->orWhereHas('orangTua', function ($qOt) use ($search) {
                      $qOt->where('nama_ayah', 'like', "%{$search}%")
                          ->orWhere('nama_ibu', 'like', "%{$search}%")
                          ->orWhere('no_wa', 'like', "%{$search}%")
                          ->orWhere('alamat', 'like', "%{$search}%");
                  });
            });
        }

        switch ($sortBy) {
            case 'nama_desc':
                $query->orderBy('nama', 'desc');
                break;
            case 'nisn':
                $query->orderBy('nisn', 'asc');
                break;
            case 'nama_asc':
            default:
                $query->orderBy('nama', 'asc');
                break;
        }

        $siswas = $query->paginate(20)->withQueryString();

        $kelasId = $kelas ? $kelas->id : null;
        $totalSiswa = $siswas->total();
        $totalL = Siswa::where('kelas_id', $kelasId)->where('jenis_kelamin', 'L')->count();
        $totalP = Siswa::where('kelas_id', $kelasId)->where('jenis_kelamin', 'P')->count();

        return view('guru.siswa', compact('kelas', 'allKelases', 'siswas', 'search', 'sortBy', 'totalSiswa', 'totalL', 'totalP'));
    }
}
