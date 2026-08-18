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
        $tanggal = $request->get('tanggal', Carbon::today()->toDateString());
        $kelasId = $request->get('kelas_id');

        $query = Kehadiran::with(['siswa.kelas', 'siswa.orangTua'])
            ->where('tanggal', $tanggal);

        if ($kelasId) {
            $query->whereHas('siswa', function ($q) use ($kelasId) {
                $q->where('kelas_id', $kelasId);
            });
        }

        $kehadirans = $query->orderBy('updated_at', 'desc')->paginate(20);
        $kelases = Kelas::all();

        return view('admin.rekap.monitoring', compact('kehadirans', 'kelases', 'tanggal', 'kelasId'));
    }

    /**
     * Rekapitulasi Presensi per Bulan/Kelas
     */
    public function rekap(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $kelasId = $request->get('kelas_id');

        $kelases = Kelas::all();

        $siswasQuery = Siswa::with(['kelas']);
        if ($kelasId) {
            $siswasQuery->where('kelas_id', $kelasId);
        }
        $siswas = $siswasQuery->get();

        $rekapData = [];
        foreach ($siswas as $siswa) {
            $hadirCount = Kehadiran::where('siswa_id', $siswa->id)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('status', 'HADIR')
                ->count();

            $terlambatCount = Kehadiran::where('siswa_id', $siswa->id)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('status', 'TERLAMBAT')
                ->count();

            $izinCount = Kehadiran::where('siswa_id', $siswa->id)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('status', 'IZIN')
                ->count();

            $sakitCount = Kehadiran::where('siswa_id', $siswa->id)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('status', 'SAKIT')
                ->count();

            $alpaCount = Kehadiran::where('siswa_id', $siswa->id)
                ->whereYear('tanggal', $tahun)
                ->whereMonth('tanggal', $bulan)
                ->where('status', 'ALPA')
                ->count();

            $rekapData[] = [
                'siswa' => $siswa,
                'hadir' => $hadirCount,
                'terlambat' => $terlambatCount,
                'izin' => $izinCount,
                'sakit' => $sakitCount,
                'alpa' => $alpaCount,
            ];
        }

        return view('admin.rekap.rekap', compact('rekapData', 'kelases', 'bulan', 'tahun', 'kelasId'));
    }

    /**
     * Cetak Laporan Presensi (Tampilan Siap Cetak Browser / PDF)
     */
    public function cetakLaporan(Request $request)
    {
        $bulan = $request->get('bulan', Carbon::now()->month);
        $tahun = $request->get('tahun', Carbon::now()->year);
        $kelasId = $request->get('kelas_id');

        $kelasObj = $kelasId ? Kelas::find($kelasId) : null;

        $siswasQuery = Siswa::with(['kelas']);
        if ($kelasId) {
            $siswasQuery->where('kelas_id', $kelasId);
        }
        $siswas = $siswasQuery->get();

        $rekapData = [];
        foreach ($siswas as $siswa) {
            $rekapData[] = [
                'siswa' => $siswa,
                'hadir' => Kehadiran::where('siswa_id', $siswa->id)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->where('status', 'HADIR')->count(),
                'terlambat' => Kehadiran::where('siswa_id', $siswa->id)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->where('status', 'TERLAMBAT')->count(),
                'izin' => Kehadiran::where('siswa_id', $siswa->id)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->where('status', 'IZIN')->count(),
                'sakit' => Kehadiran::where('siswa_id', $siswa->id)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->where('status', 'SAKIT')->count(),
                'alpa' => Kehadiran::where('siswa_id', $siswa->id)->whereYear('tanggal', $tahun)->whereMonth('tanggal', $bulan)->where('status', 'ALPA')->count(),
            ];
        }

        return view('admin.rekap.cetak', compact('rekapData', 'kelasObj', 'bulan', 'tahun'));
    }
}
