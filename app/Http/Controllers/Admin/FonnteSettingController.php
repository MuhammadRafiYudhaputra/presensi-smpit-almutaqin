<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SettingFonnte;
use Illuminate\Http\Request;

class FonnteSettingController extends Controller
{
    public function index()
    {
        $setting = SettingFonnte::first() ?? new SettingFonnte([
            'template_masuk' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi SMP IT Al-Mutaqin:\nAnanda {nama} (Kelas {kelas}) telah tiba dan melakukan presensi MASUK pada tanggal {tanggal} pukul {waktu}.\n\nTerima kasih.",
            'template_terlambat' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi TERLAMBAT SMP IT Al-Mutaqin:\nAnanda {nama} (Kelas {kelas}) melakukan presensi pada pukul {waktu} (TERLAMBAT).\nMohon bimbingan dari Bpk/Ibu Orang Tua.\n\nTerima kasih.",
            'template_pulang' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi PULANG SMP IT Al-Mutaqin:\nAnanda {nama} (Kelas {kelas}) telah melakukan presensi PULANG pada tanggal {tanggal} pukul {waktu}.\n\nTerima kasih.",
            'is_active' => true,
        ]);

        return view('admin.fonnte.index', compact('setting'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'api_token' => 'required|string',
            'template_masuk' => 'required|string',
            'template_terlambat' => 'required|string',
            'template_pulang' => 'required|string',
        ]);

        $setting = SettingFonnte::first();
        if ($setting) {
            $setting->update([
                'api_token' => $request->api_token,
                'template_masuk' => $request->template_masuk,
                'template_terlambat' => $request->template_terlambat,
                'template_pulang' => $request->template_pulang,
                'is_active' => $request->has('is_active'),
            ]);
        } else {
            SettingFonnte::create([
                'api_token' => $request->api_token,
                'template_masuk' => $request->template_masuk,
                'template_terlambat' => $request->template_terlambat,
                'template_pulang' => $request->template_pulang,
                'is_active' => $request->has('is_active'),
            ]);
        }

        return redirect()->back()->with('success', 'Pengaturan Notifikasi Fonnte WhatsApp berhasil disimpan!');
    }

}
