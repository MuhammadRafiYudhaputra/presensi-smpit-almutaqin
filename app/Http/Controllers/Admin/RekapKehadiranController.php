<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kehadiran;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RekapKehadiranController extends Controller
{
    /**
     * Absensi Siswa Harian (Daftar Presensi Harian Siswa & Set Status)
     */
    public function monitoring(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today('Asia/Jakarta')->toDateString());
        $kelasId = $request->get('kelas_id');
        $sortBy = $request->get('sort_by', 'nama_asc');

        $kelases = Kelas::all();

        $siswasQuery = Siswa::with(['kelas', 'orangTua'])
            ->whereNotNull('kelas_id')
            ->where(function ($q) {
                $q->where('status', '!=', 'alumni')->orWhereNull('status');
            });

        if ($kelasId) {
            $siswasQuery->where('kelas_id', $kelasId);
        }

        switch ($sortBy) {
            case 'nama_desc':
                $siswasQuery->orderBy('nama', 'desc');
                break;
            case 'nisn':
                $siswasQuery->orderBy('nisn', 'asc');
                break;
            case 'nama_asc':
            default:
                $siswasQuery->orderBy('nama', 'asc');
                break;
        }

        $siswas = $siswasQuery->get();
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

        foreach ($siswas as $siswa) {
            $kh = $kehadiranMap->get($siswa->id);
            $status = $kh ? $kh->status : 'BELUM ABSEN';

            if ($status === 'HADIR') $summary['hadir']++;
            elseif ($status === 'TERLAMBAT') $summary['terlambat']++;
            elseif ($status === 'IZIN') $summary['izin']++;
            elseif ($status === 'SAKIT') $summary['sakit']++;
            elseif ($status === 'ALPA') $summary['alpa']++;
            else $summary['belum']++;

            $harianData[] = (object) [
                'siswa' => $siswa,
                'jam_masuk' => $kh ? $kh->jam_masuk : null,
                'jam_pulang' => $kh ? $kh->jam_pulang : null,
                'status' => $status,
                'keterangan' => $kh ? $kh->keterangan : null,
                'wa_sent' => $kh ? $kh->wa_masuk_sent : false,
                'kehadiran_id' => $kh ? $kh->id : null,
            ];
        }

        return view('admin.rekap.monitoring', compact(
            'harianData',
            'kelases',
            'tanggal',
            'kelasId',
            'sortBy',
            'summary'
        ));
    }

    /**
     * Helper untuk menghitung default hari efektif (Senin - Jumat)
     */
    private function calculateDefaultHariEfektif($mode, $bulan, $tahun, $semester, $kelasNama = null)
    {
        if ($mode === 'bulanan') {
            $startDate = Carbon::createFromDate($tahun, $bulan, 1);
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
            // Khusus Kelas 9 Semester Genap (Semester 2) hari efektif biasanya lebih sedikit (~90 hari)
            if ($kelasNama && str_contains(strtoupper($kelasNama), '9') && $semester === 'genap') {
                return 90;
            }

            $startMonth = ($semester === 'ganjil') ? 7 : 1;
            $endMonth = ($semester === 'ganjil') ? 12 : 6;
            $startDate = Carbon::createFromDate($tahun, $startMonth, 1);
            $endDate = Carbon::createFromDate($tahun, $endMonth, 1)->endOfMonth();
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

    /**
     * Rekapitulasi Presensi (Mode Bulanan & Mode Semester)
     */
    public function rekap(Request $request)
    {
        $settingAkademik = \App\Models\SettingAkademik::getActive();
        $activeBaseYear = $settingAkademik ? (int) substr($settingAkademik->tahun_ajaran, 0, 4) : Carbon::now('Asia/Jakarta')->year;

        $mode = $request->get('mode', 'bulanan');
        if (!in_array($mode, ['bulanan', 'semester'])) {
            $mode = 'bulanan';
        }

        $tanggal = $request->get('tanggal', Carbon::today('Asia/Jakarta')->toDateString());
        $bulan = (int) $request->get('bulan', Carbon::now('Asia/Jakarta')->month);

        if ($mode === 'semester') {
            $tahun = (int) $request->get('tahun', $activeBaseYear);
            $semester = $request->get('semester', $settingAkademik->semester ?? (Carbon::now('Asia/Jakarta')->month >= 7 ? 'ganjil' : 'genap'));
        } else {
            $defaultBulanYear = ($settingAkademik && $settingAkademik->semester === 'genap') ? ($activeBaseYear + 1) : $activeBaseYear;
            $tahun = (int) $request->get('tahun', $defaultBulanYear);
            $semester = $request->get('semester', $settingAkademik->semester ?? (Carbon::now('Asia/Jakarta')->month >= 7 ? 'ganjil' : 'genap'));
        }

        $kelasId = $request->get('kelas_id');
        $sortBy = $request->get('sort_by', 'nama_asc');

        $kelases = Kelas::all();
        $selectedKelas = $kelasId ? Kelas::find($kelasId) : null;

        // Tentukan Tahun Ajaran berdasarkan periode yang dipilih
        if ($mode === 'semester') {
            $tahunAjaran = ($semester === 'ganjil') ? ($tahun . '/' . ($tahun + 1)) : (($tahun - 1) . '/' . $tahun);
        } else {
            $tahunAjaran = ($bulan >= 7) ? ($tahun . '/' . ($tahun + 1)) : (($tahun - 1) . '/' . $tahun);
        }

        // Hari Efektif (Bisa disesuaikan oleh Admin TU)
        $defaultHariEfektif = $this->calculateDefaultHariEfektif($mode, $bulan, $tahun, $semester, $selectedKelas ? $selectedKelas->nama_kelas : null);
        $hariEfektif = (int) $request->get('hari_efektif', $defaultHariEfektif);
        if ($hariEfektif <= 0) $hariEfektif = $defaultHariEfektif;

        // Query Siswa berdasarkan riwayat kelas pada tahun ajaran yang dipilih
        $hasHistory = \App\Models\RiwayatKelas::where('tahun_ajaran', $tahunAjaran)->exists();

        if ($hasHistory) {
            $siswasQuery = Siswa::whereHas('riwayatKelas', function ($q) use ($tahunAjaran, $kelasId) {
                $q->where('tahun_ajaran', $tahunAjaran);
                if ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                }
            })->with(['riwayatKelas' => function($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran', $tahunAjaran)->with('kelas');
            }, 'kelas', 'orangTua']);
        } else {
            // Fallback untuk tahun ajaran aktif jika belum ada entri riwayat
            $siswasQuery = Siswa::with(['kelas', 'orangTua'])
                ->whereNotNull('kelas_id')
                ->where(function ($q) {
                    $q->where('status', '!=', 'alumni')->orWhereNull('status');
                });

            if ($kelasId) {
                $siswasQuery->where('kelas_id', $kelasId);
            }
        }

        switch ($sortBy) {
            case 'nama_desc':
                $siswasQuery->orderBy('nama', 'desc');
                break;
            case 'nisn':
                $siswasQuery->orderBy('nisn', 'asc');
                break;
            case 'nama_asc':
            default:
                $siswasQuery->orderBy('nama', 'asc');
                break;
        }

        $siswas = $siswasQuery->get();

        // 1. Data Rekap Bulanan
        $bulananData = [];
        if ($mode === 'bulanan') {
            foreach ($siswas as $siswa) {
                $tepatWaktu = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'HADIR')
                    ->count();

                $lateQuery = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'TERLAMBAT')
                    ->orderBy('tanggal', 'asc');
                $terlambat = $lateQuery->count();
                $riwayatTerlambat = $lateQuery->get(['tanggal', 'jam_masuk']);

                $izin = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'IZIN')
                    ->count();

                $sakit = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'SAKIT')
                    ->count();

                $alpa = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'ALPA')
                    ->count();

                $totalHadir = $tepatWaktu + $terlambat;
                $persentase = ($hariEfektif > 0) ? round(($totalHadir / $hariEfektif) * 100, 1) : 0;
                $kelasHistoris = $siswa->getKelasForTahunAjaran($tahunAjaran);

                $bulananData[] = (object) [
                    'siswa' => $siswa,
                    'kelas_historis' => $kelasHistoris,
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

        // 2. Data Rekap Semester
        $semesterData = [];
        if ($mode === 'semester') {
            $startMonth = ($semester === 'ganjil') ? 7 : 1;
            $endMonth = ($semester === 'ganjil') ? 12 : 6;

            foreach ($siswas as $siswa) {
                $tepatWaktu = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'HADIR')
                    ->count();

                $lateQuery = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'TERLAMBAT')
                    ->orderBy('tanggal', 'asc');
                $terlambat = $lateQuery->count();
                $riwayatTerlambat = $lateQuery->get(['tanggal', 'jam_masuk']);

                $izin = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'IZIN')
                    ->count();

                $sakit = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'SAKIT')
                    ->count();

                $alpa = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'ALPA')
                    ->count();

                $totalHadir = $tepatWaktu + $terlambat;
                $persentase = ($hariEfektif > 0) ? round(($totalHadir / $hariEfektif) * 100, 1) : 0;
                $kelasHistoris = $siswa->getKelasForTahunAjaran($tahunAjaran);

                $semesterData[] = (object) [
                    'siswa' => $siswa,
                    'kelas_historis' => $kelasHistoris,
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

        return view('admin.rekap.rekap', compact(
            'mode',
            'tanggal',
            'bulan',
            'tahun',
            'tahunAjaran',
            'semester',
            'kelasId',
            'sortBy',
            'hariEfektif',
            'kelases',
            'settingAkademik',
            'bulananData',
            'semesterData'
        ));
    }

    /**
     * Update Status Kehadiran Manual oleh Admin/Wali Kelas
     */
    public function updateStatus(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswas,id',
            'tanggal' => 'required|date',
            'status' => 'required|in:HADIR,TERLAMBAT,IZIN,SAKIT,ALPA',
            'keterangan' => 'nullable|string|max:255',
        ]);

        $kehadiran = Kehadiran::where('siswa_id', $request->siswa_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($kehadiran) {
            $kehadiran->update([
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'jam_masuk' => in_array($request->status, ['HADIR', 'TERLAMBAT']) ? ($kehadiran->jam_masuk ?? date('H:i:s')) : null,
            ]);
        } else {
            Kehadiran::create([
                'siswa_id' => $request->siswa_id,
                'tanggal' => $request->tanggal,
                'jam_masuk' => in_array($request->status, ['HADIR', 'TERLAMBAT']) ? date('H:i:s') : null,
                'status' => $request->status,
                'keterangan' => $request->keterangan,
                'wa_masuk_sent' => false,
            ]);
        }

        return redirect()->back()->with('success', "Status kehadiran siswa berhasil diperbarui menjadi {$request->status}!");
    }

    /**
     * Cetak Laporan Rekapitulasi Presensi
     */
    public function cetakLaporan(Request $request)
    {
        $mode = $request->get('mode', 'bulanan');
        $bulan = (int) $request->get('bulan', Carbon::now()->month);
        $tahun = (int) $request->get('tahun', Carbon::now()->year);
        $semester = $request->get('semester', 'ganjil');
        $kelasId = $request->get('kelas_id');
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());

        $kelas = $kelasId ? Kelas::with('waliKelas')->find($kelasId) : null;

        // Tentukan Tahun Ajaran
        if ($mode === 'semester') {
            $tahunAjaran = ($semester === 'ganjil') ? ($tahun . '/' . ($tahun + 1)) : (($tahun - 1) . '/' . $tahun);
        } elseif ($mode === 'harian') {
            $dt = Carbon::parse($tanggal);
            $tahunAjaran = ($dt->month >= 7) ? ($dt->year . '/' . ($dt->year + 1)) : (($dt->year - 1) . '/' . $dt->year);
        } else {
            $tahunAjaran = ($bulan >= 7) ? ($tahun . '/' . ($tahun + 1)) : (($tahun - 1) . '/' . $tahun);
        }

        $defaultHariEfektif = $this->calculateDefaultHariEfektif($mode, $bulan, $tahun, $semester, $kelas ? $kelas->nama_kelas : null);
        $hariEfektif = (int) $request->get('hari_efektif', $defaultHariEfektif);
        if ($hariEfektif <= 0) $hariEfektif = $defaultHariEfektif;

        // Query Siswa berdasarkan riwayat kelas
        $hasHistory = \App\Models\RiwayatKelas::where('tahun_ajaran', $tahunAjaran)->exists();

        if ($hasHistory) {
            $siswasQuery = Siswa::whereHas('riwayatKelas', function ($q) use ($tahunAjaran, $kelasId) {
                $q->where('tahun_ajaran', $tahunAjaran);
                if ($kelasId) {
                    $q->where('kelas_id', $kelasId);
                }
            })->with(['riwayatKelas' => function($q) use ($tahunAjaran) {
                $q->where('tahun_ajaran', $tahunAjaran)->with('kelas');
            }, 'kelas']);
        } else {
            $siswasQuery = Siswa::with('kelas')
                ->whereNotNull('kelas_id')
                ->where(function ($q) {
                    $q->where('status', '!=', 'alumni')->orWhereNull('status');
                });
            if ($kelasId) {
                $siswasQuery->where('kelas_id', $kelasId);
            }
        }
        $siswas = $siswasQuery->orderBy('nama', 'asc')->get();

        $dataLaporan = [];
        foreach ($siswas as $siswa) {
            $kelasHistoris = $siswa->getKelasForTahunAjaran($tahunAjaran);

            if ($mode === 'semester') {
                $startMonth = ($semester === 'ganjil') ? 7 : 1;
                $endMonth = ($semester === 'ganjil') ? 12 : 6;
                $tepatWaktu = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'HADIR')
                    ->count();
                $terlambat = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'TERLAMBAT')
                    ->count();
                $izin = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'IZIN')
                    ->count();
                $sakit = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'SAKIT')
                    ->count();
                $alpa = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', '>=', $startMonth)
                    ->whereMonth('tanggal', '<=', $endMonth)
                    ->where('status', 'ALPA')
                    ->count();
            } elseif ($mode === 'harian') {
                $kh = Kehadiran::where('siswa_id', $siswa->id)->where('tanggal', $tanggal)->first();
                $status = $kh ? $kh->status : 'BELUM ABSEN';
                $tepatWaktu = ($status === 'HADIR') ? 1 : 0;
                $terlambat = ($status === 'TERLAMBAT') ? 1 : 0;
                $izin = ($status === 'IZIN') ? 1 : 0;
                $sakit = ($status === 'SAKIT') ? 1 : 0;
                $alpa = ($status === 'ALPA') ? 1 : 0;
            } else {
                $tepatWaktu = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'HADIR')
                    ->count();
                $terlambat = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'TERLAMBAT')
                    ->count();
                $izin = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'IZIN')
                    ->count();
                $sakit = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'SAKIT')
                    ->count();
                $alpa = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->where('status', 'ALPA')
                    ->count();
            }

            $totalHadir = $tepatWaktu + $terlambat;
            $persentase = ($hariEfektif > 0) ? round(($totalHadir / $hariEfektif) * 100, 1) : 0;

            $dataLaporan[] = (object) [
                'siswa' => $siswa,
                'kelas_historis' => $kelasHistoris,
                'hadir' => $totalHadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'persentase' => min(100, $persentase),
            ];
        }

        // Export ke format Microsoft Word (.doc) jika diminta
        if ($request->get('format') === 'doc' || $request->has('export_doc')) {
            $filename = "Laporan_Presensi_SMPIT_{$mode}_" . ($mode === 'semester' ? $semester : $bulan) . "_{$tahun}.doc";
            return response()->view('admin.rekap.cetak', compact('mode', 'bulan', 'tahun', 'semester', 'kelas', 'dataLaporan', 'tanggal', 'hariEfektif'))
                ->header('Content-Type', 'application/vnd.ms-word')
                ->header('Content-Disposition', "attachment; filename=\"{$filename}\"")
                ->header('Cache-Control', 'max-age=0');
        }

        // Export ke format CSV / Excel jika diminta
        if ($request->get('format') === 'csv' || $request->has('export_csv')) {
            $filename = "Laporan_Presensi_SMPIT_{$mode}_" . ($mode === 'semester' ? $semester : $bulan) . "_{$tahun}.csv";
            $headers = [
                'Content-Type' => 'text/csv; charset=utf-8',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ];
            $callback = function () use ($dataLaporan, $mode, $bulan, $tahun, $semester, $kelas, $hariEfektif) {
                $file = fopen('php://output', 'w');
                fputs($file, "\xEF\xBB\xBF");
                fputcsv($file, ['LAPORAN REKAPITULASI PRESENSI SISWA']);
                fputcsv($file, ['SMP ISLAM TERPADU AL-MUTTAQIN']);
                fputcsv($file, ['Periode: ' . ($mode === 'semester' ? 'Semester ' . ucfirst($semester) . " {$tahun}/" . ($tahun + 1) : 'Bulan ' . $bulan . "/{$tahun}")]);
                fputcsv($file, ['Kelas: ' . ($kelas ? $kelas->nama_kelas : 'Semua Kelas')]);
                fputcsv($file, ['Dasar Hari Efektif: ' . $hariEfektif . ' Hari']);
                fputcsv($file, []);
                fputcsv($file, ['No', 'NISN', 'Nama Peserta Didik', 'Kelas', 'Hadir', 'Terlambat (BK)', 'Izin', 'Sakit', 'Alpa', 'Persentase Kehadiran']);

                foreach ($dataLaporan as $index => $row) {
                    fputcsv($file, [
                        $index + 1,
                        "'" . $row->siswa->nisn,
                        $row->siswa->nama,
                        'Kelas ' . ($row->siswa->kelas->nama_kelas ?? '-'),
                        $row->hadir,
                        $row->terlambat,
                        $row->izin,
                        $row->sakit,
                        $row->alpa,
                        $row->persentase . '%',
                    ]);
                }
                fclose($file);
            };
            return response()->stream($callback, 200, $headers);
        }

        return view('admin.rekap.cetak', compact('mode', 'bulan', 'tahun', 'semester', 'kelas', 'dataLaporan', 'tanggal', 'hariEfektif'));
    }

    /**
     * Halaman Menu Generate Laporan Khusus Siswa
     */
    public function generateLaporanIndex()
    {
        $kelases = Kelas::withCount(['siswas' => function ($q) {
            $q->where('status', '!=', 'alumni')->orWhereNull('status');
        }])->get();

        $totalSiswa = Siswa::where('status', '!=', 'alumni')->orWhereNull('status')->count();

        return view('admin.laporan.index', compact('kelases', 'totalSiswa'));
    }
}
