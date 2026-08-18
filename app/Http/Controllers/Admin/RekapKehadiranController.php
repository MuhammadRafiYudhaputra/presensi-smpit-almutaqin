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
     * Live Monitoring Kehadiran Siswa
     */
    public function monitoring(Request $request)
    {
        $tanggal = $request->get('tanggal', Carbon::today('Asia/Jakarta')->toDateString());
        $kelasId = $request->get('kelas_id');

        $query = Kehadiran::with(['siswa.kelas', 'siswa.orangTua'])
            ->where('tanggal', $tanggal)
            ->whereHas('siswa', function ($q) {
                $q->where('status', '!=', 'alumni')->orWhereNull('status');
            });

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $kehadirans = $query->orderBy('updated_at', 'desc')->paginate(20)->withQueryString();
        $kelases = Kelas::all();

        return view('admin.rekap.monitoring', compact('kehadirans', 'kelases', 'tanggal', 'kelasId'));
    }

    /**
     * Rekapitulasi Presensi 3 Mode (Harian, Bulanan, Semester)
     */
    public function rekap(Request $request)
    {
        $mode = $request->get('mode', 'harian');
        $tanggal = $request->get('tanggal', Carbon::today('Asia/Jakarta')->toDateString());
        $bulan = (int) $request->get('bulan', Carbon::now('Asia/Jakarta')->month);
        $tahun = (int) $request->get('tahun', Carbon::now('Asia/Jakarta')->year);
        $semester = $request->get('semester', (Carbon::now('Asia/Jakarta')->month >= 7 ? 'ganjil' : 'genap'));
        $kelasId = $request->get('kelas_id');
        $sortBy = $request->get('sort_by', 'nama_asc');

        $kelases = Kelas::all();

        // Query Siswa Aktif
        $siswasQuery = Siswa::with('kelas')
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

        // 1. Data Rekap Harian
        $harianData = [];
        if ($mode === 'harian') {
            $kehadiranMap = Kehadiran::where('tanggal', $tanggal)->get()->keyBy('siswa_id');
            foreach ($siswas as $siswa) {
                $kh = $kehadiranMap->get($siswa->id);
                $harianData[] = (object) [
                    'siswa' => $siswa,
                    'jam_masuk' => $kh ? $kh->jam_masuk : null,
                    'jam_pulang' => $kh ? $kh->jam_pulang : null,
                    'status' => $kh ? $kh->status : 'ALPA',
                    'kehadiran_id' => $kh ? $kh->id : null,
                ];
            }
        }

        // 2. Data Rekap Bulanan
        $bulananData = [];
        if ($mode === 'bulanan') {
            $totalHariEfektif = 24; // Standar 24 hari sekolah per bulan
            foreach ($siswas as $siswa) {
                $hadir = Kehadiran::where('siswa_id', $siswa->id)
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

                $totalHadirFisik = $hadir + $terlambat;
                $persentase = ($totalHariEfektif > 0) ? round(($totalHadirFisik / $totalHariEfektif) * 100, 1) : 0;

                $bulananData[] = (object) [
                    'siswa' => $siswa,
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                    'persentase' => min(100, $persentase),
                ];
            }
        }

        // 3. Data Rekap Semester
        $semesterData = [];
        if ($mode === 'semester') {
            $startMonth = ($semester === 'ganjil') ? 7 : 1;
            $endMonth = ($semester === 'ganjil') ? 12 : 6;
            $totalHariSemester = 120; // Standar 120 hari per semester

            foreach ($siswas as $siswa) {
                $hadir = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) as integer)'), [$startMonth, $endMonth])
                    ->where('status', 'HADIR')
                    ->count();

                $terlambat = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) as integer)'), [$startMonth, $endMonth])
                    ->where('status', 'TERLAMBAT')
                    ->count();

                $izin = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) as integer)'), [$startMonth, $endMonth])
                    ->where('status', 'IZIN')
                    ->count();

                $sakit = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) as integer)'), [$startMonth, $endMonth])
                    ->where('status', 'SAKIT')
                    ->count();

                $alpa = Kehadiran::where('siswa_id', $siswa->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) as integer)'), [$startMonth, $endMonth])
                    ->where('status', 'ALPA')
                    ->count();

                $totalHadirFisik = $hadir + $terlambat;
                $persentase = ($totalHariSemester > 0) ? round(($totalHadirFisik / $totalHariSemester) * 100, 1) : 0;

                $semesterData[] = (object) [
                    'siswa' => $siswa,
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
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
            'semester',
            'kelasId',
            'sortBy',
            'kelases',
            'harianData',
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
        ]);

        $kehadiran = Kehadiran::where('siswa_id', $request->siswa_id)
            ->where('tanggal', $request->tanggal)
            ->first();

        if ($kehadiran) {
            $kehadiran->update([
                'status' => $request->status,
                'jam_masuk' => in_array($request->status, ['HADIR', 'TERLAMBAT']) ? ($kehadiran->jam_masuk ?? date('H:i:s')) : null,
            ]);
        } else {
            Kehadiran::create([
                'siswa_id' => $request->siswa_id,
                'tanggal' => $request->tanggal,
                'jam_masuk' => in_array($request->status, ['HADIR', 'TERLAMBAT']) ? date('H:i:s') : null,
                'status' => $request->status,
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

        $siswasQuery = Siswa::with('kelas')->where('status', '!=', 'alumni')->orWhereNull('status');
        if ($kelasId) {
            $siswasQuery->where('kelas_id', $kelasId);
        }
        $siswas = $siswasQuery->orderBy('nama', 'asc')->get();

        $dataLaporan = [];
        foreach ($siswas as $siswa) {
            $hadir = Kehadiran::where('siswa_id', $siswa->id)
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

            $dataLaporan[] = (object) [
                'siswa' => $siswa,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'izin' => $izin,
                'sakit' => $sakit,
                'alpa' => $alpa,
                'persentase' => round((($hadir + $terlambat) / 24) * 100, 1),
            ];
        }

        return view('admin.rekap.cetak', compact('mode', 'bulan', 'tahun', 'semester', 'kelas', 'dataLaporan', 'tanggal'));
    }
}
