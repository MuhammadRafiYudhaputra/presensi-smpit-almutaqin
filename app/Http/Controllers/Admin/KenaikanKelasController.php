<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KenaikanKelasController extends Controller
{
    public function proses(Request $request)
    {
        $exceptIds = $request->input('except_siswa_ids', []);
        if (!is_array($exceptIds)) {
            $exceptIds = [];
        }

        DB::beginTransaction();
        try {
            // Ambil mapping kelas berdasarkan tingkatan angka (7, 8, 9)
            $kelas7 = Kelas::where('nama_kelas', 'like', '7%')->orWhere('nama_kelas', '7')->get();
            $kelas8 = Kelas::where('nama_kelas', 'like', '8%')->orWhere('nama_kelas', '8')->get();
            $kelas9 = Kelas::where('nama_kelas', 'like', '9%')->orWhere('nama_kelas', '9')->get();

            $kelas8Target = $kelas8->first();
            $kelas9Target = $kelas9->first();

            // 1. Siswa Kelas 9 -> Lulus menjadi ALUMNI (Kecuali yang tinggal kelas)
            $siswaKelas9 = Siswa::whereIn('kelas_id', $kelas9->pluck('id'))
                ->where('status', '!=', 'alumni')
                ->whereNotIn('id', $exceptIds)
                ->get();

            foreach ($siswaKelas9 as $siswa) {
                $siswa->update([
                    'status' => 'alumni',
                    'kelas_id' => null,
                ]);
            }

            // 2. Siswa Kelas 8 -> Naik ke Kelas 9 (Kecuali yang tinggal kelas)
            if ($kelas9Target) {
                Siswa::whereIn('kelas_id', $kelas8->pluck('id'))
                    ->where('status', '!=', 'alumni')
                    ->whereNotIn('id', $exceptIds)
                    ->update([
                        'kelas_id' => $kelas9Target->id,
                        'status' => 'aktif',
                    ]);
            }

            // 3. Siswa Kelas 7 -> Naik ke Kelas 8 (Kecuali yang tinggal kelas)
            if ($kelas8Target) {
                Siswa::whereIn('kelas_id', $kelas7->pluck('id'))
                    ->where('status', '!=', 'alumni')
                    ->whereNotIn('id', $exceptIds)
                    ->update([
                        'kelas_id' => $kelas8Target->id,
                        'status' => 'aktif',
                    ]);
            }

            DB::commit();

            $jumlahLulus = $siswaKelas9->count();
            $jumlahTinggal = count($exceptIds);

            return redirect()->back()->with('success', "Proses Kenaikan Kelas Tahun Ajaran Baru Berhasil! ({$jumlahLulus} siswa Kelas 9 menjadi Alumni, {$jumlahTinggal} siswa tinggal kelas dipertahankan).");
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal memproses kenaikan kelas: ' . $e->getMessage());
        }
    }
}
