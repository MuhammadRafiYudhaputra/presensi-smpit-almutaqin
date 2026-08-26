<?php

namespace App\Services;

use App\Models\Siswa;
use App\Models\Kehadiran;
use App\Models\JamPresensi;
use App\Jobs\SendWhatsAppNotificationJob;
use Carbon\Carbon;

class PresensiService
{
    /**
     * Process Scan QR Code for Student Attendance
     */
    public function processScan(string $qrToken): array
    {
        $qrToken = trim($qrToken);
        $siswa = Siswa::with(['kelas', 'orangTua'])
            ->where('qr_code_token', $qrToken)
            ->orWhere('nisn', $qrToken)
            ->orWhere('nis', $qrToken)
            ->first();

        if (!$siswa) {
            return [
                'success' => false,
                'message' => 'Kartu QR Code atau NISN [' . $qrToken . '] tidak terdaftar!',
            ];
        }

        $today = Carbon::today()->toDateString();
        $nowTime = Carbon::now()->format('H:i:s');

        // Ambil pengaturan jam presensi aktif (atau fallback default)
        $jamSetting = JamPresensi::where('is_active', true)->first();
        $jamTerlambat = $jamSetting ? $jamSetting->jam_terlambat : '07:15:00';
        $jamPulangStandard = $jamSetting ? $jamSetting->jam_pulang : '15:00:00';

        // Cek record presensi hari ini
        $kehadiran = Kehadiran::where('siswa_id', $siswa->id)
            ->where('tanggal', $today)
            ->first();

        if (!$kehadiran) {
            // PRESENSI MASUK
            $status = ($nowTime > $jamTerlambat) ? 'TERLAMBAT' : 'HADIR';

            $kehadiran = Kehadiran::create([
                'siswa_id' => $siswa->id,
                'tanggal' => $today,
                'jam_masuk' => $nowTime,
                'status' => $status,
                'wa_masuk_sent' => false,
            ]);

            // Dispatch background WhatsApp Notification
            SendWhatsAppNotificationJob::dispatch($kehadiran->id, 'masuk');

            $statusText = ($status === 'TERLAMBAT') ? 'TERLAMBAT' : 'HADIR';
            return [
                'success' => true,
                'type' => 'masuk',
                'status' => $status,
                'message' => "Presensi MASUK Berhasil! [{$siswa->nama} - Kelas {$siswa->kelas->nama_kelas}] Status: {$statusText} ({$nowTime})",
                'siswa' => $siswa,
                'waktu' => $nowTime,
            ];
        } else {
            // PRESENSI PULANG / SUDAH PRESENSI
            if (empty($kehadiran->jam_pulang)) {
                // Cegah Scan Pulang Jika Belum Waktunya
                if ($nowTime < $jamPulangStandard) {
                    return [
                        'success' => false,
                        'type' => 'belum_pulang',
                        'message' => "BELUM WAKTUNYA PULANG! Siswa [{$siswa->nama}] sudah tercatat Masuk pukul {$kehadiran->jam_masuk}. Jam pulang resmi sekolah adalah pukul {$jamPulangStandard}.",
                        'siswa' => $siswa,
                        'waktu' => $nowTime,
                    ];
                }

                // Catat Jam Pulang
                $kehadiran->update([
                    'jam_pulang' => $nowTime,
                ]);

                // Dispatch WhatsApp Notification Pulang
                SendWhatsAppNotificationJob::dispatch($kehadiran->id, 'pulang');

                return [
                    'success' => true,
                    'type' => 'pulang',
                    'status' => $kehadiran->status,
                    'message' => "Presensi PULANG Berhasil! [{$siswa->nama}] Pukul {$nowTime}",
                    'siswa' => $siswa,
                    'waktu' => $nowTime,
                ];
            } else {
                return [
                    'success' => false,
                    'message' => "Siswa [{$siswa->nama}] sudah menyelesaikan presensi masuk ({$kehadiran->jam_masuk}) dan pulang ({$kehadiran->jam_pulang}) hari ini.",
                    'siswa' => $siswa,
                ];
            }
        }
    }
}
