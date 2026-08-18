<?php

namespace App\Services;

use App\Models\SettingFonnte;
use App\Models\Kehadiran;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FonnteService
{
    /**
     * Send WhatsApp notification via Fonnte API
     */
    public function sendPresensiNotification(Kehadiran $kehadiran, string $tipe = 'masuk'): bool
    {
        $setting = SettingFonnte::first();
        if (!$setting || !$setting->is_active || empty($setting->api_token)) {
            Log::warning("Fonnte API: Token tidak dikonfigurasi atau setting non-aktif.");
            return false;
        }

        $siswa = $kehadiran->siswa;
        if (!$siswa || !$siswa->orangTua || empty($siswa->orangTua->no_wa)) {
            Log::warning("Fonnte API: Nomor WhatsApp orang tua siswa [{$siswa->nama}] tidak ditemukan.");
            return false;
        }

        $target = $siswa->orangTua->no_wa;

        // Pilih template berdasarkan tipe presensi & status
        if ($tipe === 'masuk') {
            $template = ($kehadiran->status === 'TERLAMBAT') 
                ? ($setting->template_terlambat ?? "Pemberitahuan: Ananda {nama} (Kelas {kelas}) hadir TERLAMBAT pada pukul {waktu}.")
                : ($setting->template_masuk ?? "Pemberitahuan: Ananda {nama} (Kelas {kelas}) telah tiba di sekolah SMP IT Al-Mutaqin pada pukul {waktu}.");
        } else {
            $template = $setting->template_pulang ?? "Pemberitahuan: Ananda {nama} (Kelas {kelas}) telah melakukan presensi PULANG pada pukul {waktu}.";
        }

        // Replace Placeholders
        $message = str_replace(
            ['{nama}', '{nisn}', '{kelas}', '{tanggal}', '{waktu}', '{status}'],
            [
                $siswa->nama,
                $siswa->nisn,
                $siswa->kelas ? $siswa->kelas->nama_kelas : '-',
                $kehadiran->tanggal,
                ($tipe === 'masuk' ? $kehadiran->jam_masuk : $kehadiran->jam_pulang) ?? date('H:i:s'),
                $kehadiran->status
            ],
            $template
        );

        return $this->sendRawMessage($setting->api_token, $target, $message);
    }

    /**
     * Send raw HTTP request to Fonnte API
     */
    public function sendRawMessage(string $token, string $target, string $message): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => $token,
            ])->post('https://api.fonnte.com/send', [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62', // Default Indonesia
            ]);

            if ($response->successful()) {
                Log::info("Fonnte WA Sent to {$target}: " . $response->body());
                return true;
            } else {
                Log::error("Fonnte WA Error to {$target}: " . $response->body());
                return false;
            }
        } catch (\Exception $e) {
            Log::error("Fonnte Exception: " . $e->getMessage());
            return false;
        }
    }
}
