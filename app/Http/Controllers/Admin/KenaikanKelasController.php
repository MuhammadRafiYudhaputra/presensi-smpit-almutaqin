<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    public function index(Request $request)
    {
        $kelases = Kelas::with(['siswas' => function ($q) {
            $q->where('status', '!=', 'alumni')->orWhereNull('status');
        }])->get();

        $siswaKelas7 = Siswa::whereHas('kelas', function ($q) {
            $q->where('nama_kelas', 'like', '7%')->orWhere('nama_kelas', '7');
        })->where(function ($q) {
            $q->where('status', '!=', 'alumni')->orWhereNull('status');
        })->get();

        $siswaKelas8 = Siswa::whereHas('kelas', function ($q) {
            $q->where('nama_kelas', 'like', '8%')->orWhere('nama_kelas', '8');
        })->where(function ($q) {
            $q->where('status', '!=', 'alumni')->orWhereNull('status');
        })->get();

        $siswaKelas9 = Siswa::whereHas('kelas', function ($q) {
            $q->where('nama_kelas', 'like', '9%')->orWhere('nama_kelas', '9');
        })->where(function ($q) {
            $q->where('status', '!=', 'alumni')->orWhereNull('status');
        })->get();

        $alumniCount = Siswa::where('status', 'alumni')->count();

        return view('admin.kelas.kenaikan', compact('kelases', 'siswaKelas7', 'siswaKelas8', 'siswaKelas9', 'alumniCount'));
    }

    public function proses(Request $request)
    {
        $exceptIds = $request->input('except_siswa_ids', []);
        if (!is_array($exceptIds)) {
            $exceptIds = [];
        }

        DB::beginTransaction();
        try {
            $kelas7 = Kelas::where('nama_kelas', 'like', '7%')->orWhere('nama_kelas', '7')->get();
            $kelas8 = Kelas::where('nama_kelas', 'like', '8%')->orWhere('nama_kelas', '8')->get();
            $kelas9 = Kelas::where('nama_kelas', 'like', '9%')->orWhere('nama_kelas', '9')->get();

            $kelas8Target = $kelas8->first();
            $kelas9Target = $kelas9->first();

            // 1. Siswa Kelas 9 -> Lulus menjadi ALUMNI (Kecuali yang tinggal kelas)
            $siswaKelas9 = Siswa::whereIn('kelas_id', $kelas9->pluck('id'))
                ->where(function ($q) {
                    $q->where('status', '!=', 'alumni')->orWhereNull('status');
                })
                ->whereNotIn('id', $exceptIds)
                ->get();

            foreach ($siswaKelas9 as $siswa) {
                $siswa->update([
                    'status' => 'alumni',
                    'kelas_id' => null,
                ]);
            }

            // 2. Siswa Kelas 8 -> Naik ke Kelas 9 (Kecuali yang tinggal kelas)
            $countNaikKelas9 = 0;
            if ($kelas9Target) {
                $countNaikKelas9 = Siswa::whereIn('kelas_id', $kelas8->pluck('id'))
                    ->where(function ($q) {
                        $q->where('status', '!=', 'alumni')->orWhereNull('status');
                    })
                    ->whereNotIn('id', $exceptIds)
                    ->update([
                        'kelas_id' => $kelas9Target->id,
                        'status' => 'aktif',
                    ]);
            }

            // 3. Siswa Kelas 7 -> Naik ke Kelas 8 (Kecuali yang tinggal kelas)
            $countNaikKelas8 = 0;
            if ($kelas8Target) {
                $countNaikKelas8 = Siswa::whereIn('kelas_id', $kelas7->pluck('id'))
                    ->where(function ($q) {
                        $q->where('status', '!=', 'alumni')->orWhereNull('status');
                    })
                    ->whereNotIn('id', $exceptIds)
                    ->update([
                        'kelas_id' => $kelas8Target->id,
                        'status' => 'aktif',
                    ]);
            }

            DB::commit();

            $jumlahLulus = $siswaKelas9->count();
            $jumlahTinggal = count($exceptIds);

            return redirect()->route('admin.kelas.index')->with('success', "Proses Kenaikan Kelas Tahun Ajaran Baru Berhasil Diproses! ({$countNaikKelas8} Siswa naik ke Kelas 8, {$countNaikKelas9} Siswa naik ke Kelas 9, {$jumlahLulus} Siswa Kelas 9 Lulus menjadi Alumni, dan {$jumlahTinggal} Siswa tinggal kelas dipertahankan).");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses kenaikan kelas: ' . $e->getMessage());
        }
    }

    public function pindahRombel(Request $request)
    {
        $request->validate([
            'siswa_ids' => 'required|array|min:1',
            'target_kelas_id' => 'required|exists:kelas,id',
        ]);

        $targetKelas = Kelas::findOrFail($request->target_kelas_id);

        Siswa::whereIn('id', $request->siswa_ids)->update([
            'kelas_id' => $targetKelas->id,
            'status' => 'aktif',
        ]);

        return redirect()->back()->with('success', count($request->siswa_ids) . " Siswa berhasil dipindahkan ke Kelas {$targetKelas->nama_kelas}!");
    }
}
