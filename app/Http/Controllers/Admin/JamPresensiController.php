<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\JamPresensi;
use Illuminate\Http\Request;

class JamPresensiController extends Controller
{
    public function index()
    {
        $jamPresensi = JamPresensi::where('is_active', true)->first() ?? JamPresensi::first() ?? new JamPresensi([
            'nama_jadwal' => 'Jadwal Reguler Sekolah',
            'jam_masuk' => '07:00:00',
            'jam_terlambat' => '07:15:00',
            'jam_pulang' => '15:00:00',
            'is_active' => true,
        ]);

        return view('admin.jampresensi.index', compact('jamPresensi'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'nama_jadwal' => 'required|string|max:100',
            'jam_masuk' => 'required',
            'jam_terlambat' => 'required',
            'jam_pulang' => 'required',
        ]);

        // Format waktu agar selalu H:i:s
        $formatTime = function ($time) {
            $parts = explode(':', trim($time));
            if (count($parts) === 2) {
                return $parts[0] . ':' . $parts[1] . ':00';
            }
            return $time;
        };

        $jamMasuk = $formatTime($request->jam_masuk);
        $jamTerlambat = $formatTime($request->jam_terlambat);
        $jamPulang = $formatTime($request->jam_pulang);

        $jamPresensi = JamPresensi::where('is_active', true)->first() ?? JamPresensi::first();

        if ($jamPresensi) {
            $jamPresensi->update([
                'nama_jadwal' => $request->nama_jadwal,
                'jam_masuk' => $jamMasuk,
                'jam_terlambat' => $jamTerlambat,
                'jam_pulang' => $jamPulang,
                'is_active' => true,
            ]);
        } else {
            JamPresensi::create([
                'nama_jadwal' => $request->nama_jadwal,
                'jam_masuk' => $jamMasuk,
                'jam_terlambat' => $jamTerlambat,
                'jam_pulang' => $jamPulang,
                'is_active' => true,
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan Jam Operasional Presensi berhasil diperbarui!');
    }
}
