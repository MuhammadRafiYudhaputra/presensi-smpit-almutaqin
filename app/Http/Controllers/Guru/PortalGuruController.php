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
            $daysInMonth = Carbon::createFromDate($tahun, $bulan, 1)->daysInMonth;
            foreach ($siswas as $s) {
                $khs = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereMonth('tanggal', $bulan)
                    ->get();

                $hadir = $khs->where('status', 'HADIR')->count();
                $terlambat = $khs->where('status', 'TERLAMBAT')->count();
                $izin = $khs->where('status', 'IZIN')->count();
                $sakit = $khs->where('status', 'SAKIT')->count();
                $alpa = $khs->where('status', 'ALPA')->count();

                $effectiveDays = max(1, $hadir + $terlambat + $izin + $sakit + $alpa);
                $persentase = round((($hadir + $terlambat) / $effectiveDays) * 100);

                $bulananData[] = (object)[
                    'siswa' => $s,
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                    'persentase' => $persentase,
                ];
            }
        } elseif ($mode === 'semester') {
            $startMonth = ($semester === 'ganjil') ? 7 : 1;
            $endMonth = ($semester === 'ganjil') ? 12 : 6;

            foreach ($siswas as $s) {
                $khs = Kehadiran::where('siswa_id', $s->id)
                    ->whereYear('tanggal', $tahun)
                    ->whereBetween(\DB::raw('CAST(strftime("%m", tanggal) AS INTEGER)'), [$startMonth, $endMonth])
                    ->get();

                $hadir = $khs->where('status', 'HADIR')->count();
                $terlambat = $khs->where('status', 'TERLAMBAT')->count();
                $izin = $khs->where('status', 'IZIN')->count();
                $sakit = $khs->where('status', 'SAKIT')->count();
                $alpa = $khs->where('status', 'ALPA')->count();

                $effectiveDays = max(1, $hadir + $terlambat + $izin + $sakit + $alpa);
                $persentase = round((($hadir + $terlambat) / $effectiveDays) * 100);

                $semesterData[] = (object)[
                    'siswa' => $s,
                    'hadir' => $hadir,
                    'terlambat' => $terlambat,
                    'izin' => $izin,
                    'sakit' => $sakit,
                    'alpa' => $alpa,
                    'persentase' => $persentase,
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
            'harianData',
            'bulananData',
            'semesterData'
        ));
    }
}
