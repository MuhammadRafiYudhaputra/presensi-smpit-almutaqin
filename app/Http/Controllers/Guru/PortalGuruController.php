<?php

namespace App\Http\Controllers\Guru;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Siswa;
use App\Models\Kelas;
use App\Models\Guru;
use App\Models\OrangTua;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class PortalGuruController extends Controller
{
    /**
     * Dapatkan kelas yang diampu secara eksklusif oleh Wali Kelas yang sedang login
     */
    private function getGuruKelas()
    {
        $user = Auth::user();
        if (!$user) return null;

        $guru = $user->guru ?: Guru::where('user_id', $user->id)->first();
        if (!$guru) return null;

        return Kelas::where('guru_id', $guru->id)->first();
    }

    /**
     * Absensi Siswa Harian Kelas Binaan
     */
    public function monitoring(Request $request)
    {
        $kelas = $this->getGuruKelas();
        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $sortBy = $request->get('sort_by', 'nama_asc');

        $query = Siswa::with(['kelas', 'orangTua'])
            ->whereNotNull('kelas_id')
            ->where(function ($q) {
                $q->where('status', '!=', 'alumni')->orWhereNull('status');
            });

        if ($kelas) {
            $query->where('kelas_id', $kelas->id);
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

        $siswas = $query->get();
        $kehadiranMap = Kehadiran::where('tanggal', $tanggal)->get()->keyBy('siswa_id');

        $harianData = [];
        $summary = [
            'total' => $siswas->count(),
            'hadir' => 0,
            'terlambat' => 0,
            'izin' => 0,
            'sakit' => 0,
            'alpa' => 0,
            'belum' => 0,
        ];

        foreach ($siswas as $s) {
            $kh = $kehadiranMap->get($s->id);
            $status = $kh ? $kh->status : 'BELUM ABSEN';

            if ($status === 'HADIR') $summary['hadir']++;
            elseif ($status === 'TERLAMBAT') $summary['terlambat']++;
            elseif ($status === 'IZIN') $summary['izin']++;
            elseif ($status === 'SAKIT') $summary['sakit']++;
            elseif ($status === 'ALPA') $summary['alpa']++;
            else $summary['belum']++;

            $harianData[] = (object) [
                'siswa' => $s,
                'jam_masuk' => $kh ? $kh->jam_masuk : null,
                'jam_pulang' => $kh ? $kh->jam_pulang : null,
                'status' => $status,
                'keterangan' => $kh ? $kh->keterangan : null,
                'wa_sent' => $kh ? $kh->wa_masuk_sent : false,
                'kehadiran_id' => $kh ? $kh->id : null,
            ];
        }

        return view('guru.monitoring', compact('kelas', 'tanggal', 'sortBy', 'harianData', 'summary'));
    }

    /**
     * Rekapitulasi Presensi Berkala (Bulanan & Semester)
     */
    public function rekap(Request $request)
    {
        $kelas = $this->getGuruKelas();
        $mode = $request->get('mode', 'bulanan');
        if (!in_array($mode, ['bulanan', 'semester'])) {
            $mode = 'bulanan';
        }

        $settingAkademik = \App\Models\SettingAkademik::getActive();
        $activeBaseYear = $settingAkademik ? (int) substr($settingAkademik->tahun_ajaran, 0, 4) : (int) date('Y');

        $tanggal = $request->get('tanggal', date('Y-m-d'));
        $bulan = (int)$request->get('bulan', date('n'));

        if ($mode === 'semester') {
            $tahun = (int) $request->get('tahun', $activeBaseYear);
            $semester = $request->get('semester', $settingAkademik->semester ?? (date('n') >= 7 ? 'ganjil' : 'genap'));
        } else {
            $defaultBulanYear = ($settingAkademik && $settingAkademik->semester === 'genap') ? ($activeBaseYear + 1) : $activeBaseYear;
            $tahun = (int) $request->get('tahun', $defaultBulanYear);
            $semester = $request->get('semester', $settingAkademik->semester ?? (date('n') >= 7 ? 'ganjil' : 'genap'));
        }

        $sortBy = $request->get('sort_by', 'nama_asc');

        // Tentukan Tahun Ajaran
        if ($mode === 'semester') {
            $tahunAjaran = ($semester === 'ganjil') ? ($tahun . '/' . ($tahun + 1)) : (($tahun - 1) . '/' . $tahun);
        } else {
            $tahunAjaran = ($bulan >= 7) ? ($tahun . '/' . ($tahun + 1)) : (($tahun - 1) . '/' . $tahun);
        }

        $defaultHariEfektif = $this->calculateDefaultHariEfektif($mode, $bulan, $tahun, $semester, $kelas ? $kelas->nama_kelas : null);
        $hariEfektif = (int) $request->get('hari_efektif', $defaultHariEfektif);
        if ($hariEfektif <= 0) $hariEfektif = $defaultHariEfektif;

        // Query Siswa strictly for this class on the selected academic year
        $hasHistory = \App\Models\RiwayatKelas::where('tahun_ajaran', $tahunAjaran)->exists();

        if ($hasHistory && $kelas) {
            $siswaQuery = Siswa::whereHas('riwayatKelas', function ($q) use ($tahunAjaran, $kelas) {
                $q->where('tahun_ajaran', $tahunAjaran)->where('kelas_id', $kelas->id);
            })->with(['riwayatKelas' => function($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran', $tahunAjaran)->with('kelas');
            }, 'kelas', 'orangTua']);
        } else {
            $siswaQuery = Siswa::with(['kelas', 'orangTua'])
                ->whereNotNull('kelas_id')
                ->where(function ($q) {
                    $q->where('status', '!=', 'alumni')->orWhereNull('status');
                });

            if ($kelas) {
                $siswaQuery->where('kelas_id', $kelas->id);
            }
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

        $bulananData = [];
        $semesterData = [];

        if ($mode === 'bulanan') {
            foreach ($siswas as $s) {
                $tepatWaktu = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'HADIR')
                    ->count();

                $lateQuery = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'TERLAMBAT')
                    ->orderBy('tanggal', 'asc');
                $terlambat = $lateQuery->count();
                $riwayatTerlambat = $lateQuery->get(['tanggal', 'jam_masuk']);

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
                    'riwayat_terlambat' => $riwayatTerlambat,
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
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'HADIR')
                    ->count();

                $lateQuery = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'TERLAMBAT')
                    ->orderBy('tanggal', 'asc');
                $terlambat = $lateQuery->count();
                $riwayatTerlambat = $lateQuery->get(['tanggal', 'jam_masuk']);

                $izin = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'IZIN')
                    ->count();

                $sakit = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'SAKIT')
                    ->count();

                $alpa = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'ALPA')
                    ->count();

                $totalHadir = $tepatWaktu + $terlambat;
                $persentase = ($hariEfektif > 0) ? round(($totalHadir / $hariEfektif) * 100, 1) : 0;

                $semesterData[] = (object)[
                    'siswa' => $s,
                    'hadir' => $totalHadir,
                    'terlambat' => $terlambat,
                    'riwayat_terlambat' => $riwayatTerlambat,
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
            'tahunAjaran',
            'semester',
            'sortBy',
            'hariEfektif',
            'settingAkademik',
            'bulananData',
            'semesterData'
        ));
    }

    /**
     * Data Orang Tua / Wali Siswa Binaan Wali Kelas
     */
    public function siswa(Request $request)
    {
        $kelas = $this->getGuruKelas();
        $search = $request->get('search');
        $sortBy = $request->get('sort_by', 'ayah_asc');

        $query = OrangTua::with(['siswas' => function($q) use ($kelas) {
            $q->where(function($sq) {
                $sq->where('status', '!=', 'alumni')->orWhereNull('status');
            });
            if ($kelas) {
                $q->where('kelas_id', $kelas->id);
            }
        }, 'siswas.kelas']);

        if ($kelas) {
            $query->whereHas('siswas', function ($q) use ($kelas) {
                $q->where('kelas_id', $kelas->id)
                  ->where(function($sq) {
                      $sq->where('status', '!=', 'alumni')->orWhereNull('status');
                  });
            });
        }

        if (!empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_ayah', 'like', "%{$search}%")
                  ->orWhere('nama_ibu', 'like', "%{$search}%")
                  ->orWhere('nama_wali', 'like', "%{$search}%")
                  ->orWhere('hubungan_wali', 'like', "%{$search}%")
                  ->orWhere('no_wa', 'like', "%{$search}%")
                  ->orWhere('alamat', 'like', "%{$search}%")
                  ->orWhereHas('siswas', function ($qs) use ($search) {
                      $qs->where('nama', 'like', "%{$search}%")
                         ->orWhere('nisn', 'like', "%{$search}%")
                         ->orWhere('nis', 'like', "%{$search}%");
                  });
            });
        }

        switch ($sortBy) {
            case 'ayah_desc':
                $query->orderBy('nama_ayah', 'desc');
                break;
            case 'ibu_asc':
                $query->orderBy('nama_ibu', 'asc');
                break;
            case 'wali_asc':
                $query->orderBy('nama_wali', 'asc');
                break;
            case 'no_wa':
                $query->orderBy('no_wa', 'asc');
                break;
            case 'ayah_asc':
            default:
                $query->orderBy('nama_ayah', 'asc');
                break;
        }

        $orangTuas = $query->paginate(15)->withQueryString();

        return view('guru.siswa', compact('kelas', 'orangTuas', 'search', 'sortBy'));
    }

    /**
     * Helper untuk menghitung default hari efektif (Senin - Jumat tanpa Libur Nasional)
     */
    private function calculateDefaultHariEfektif($mode, $bulan, $tahun, $semester, $kelasNama = null)
    {
        if ($mode === 'bulanan') {
            $startDate = \Carbon\Carbon::createFromDate($tahun, $bulan, 1);
            $endDate = $startDate->copy()->endOfMonth();
            $effectiveDays = 0;
            $current = $startDate->copy();
            while ($current <= $endDate) {
                if (!\App\Helpers\HolidayHelper::isNonEffectiveDay($current)) {
                    $effectiveDays++;
                }
                $current->addDay();
            }
            return max(1, $effectiveDays);
        } elseif ($mode === 'semester') {
            if ($kelasNama && str_contains(strtoupper($kelasNama), '9') && $semester === 'genap') {
                return 90;
            }

            $startMonth = ($semester === 'ganjil') ? 7 : 1;
            $endMonth = ($semester === 'ganjil') ? 12 : 6;
            $startDate = \Carbon\Carbon::createFromDate($tahun, $startMonth, 1);
            $endDate = \Carbon\Carbon::createFromDate($tahun, $endMonth, 1)->endOfMonth();
            $effectiveDays = 0;
            $current = $startDate->copy();
            while ($current <= $endDate) {
                if (!\App\Helpers\HolidayHelper::isNonEffectiveDay($current)) {
                    $effectiveDays++;
                }
                $current->addDay();
            }
            return max(1, $effectiveDays);
        }
        return 1;
    }
}
