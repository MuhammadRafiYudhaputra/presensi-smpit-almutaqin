<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Guru;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Siswa;
use App\Models\JamPresensi;
use App\Models\SettingFonnte;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin User
        User::create([
            'name' => 'Admin TU SMP IT Al-Mutaqin',
            'email' => 'admin@almutaqin.sch.id',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Guru User
        $userGuru = User::create([
            'name' => 'Ustadz Ahmad, S.Pd',
            'email' => 'guru@almutaqin.sch.id',
            'password' => bcrypt('password'),
            'role' => 'guru',
        ]);

        $guru = Guru::create([
            'user_id' => $userGuru->id,
            'nip' => '198501012010011001',
            'nama' => 'Ustadz Ahmad, S.Pd',
            'no_hp' => '081234567890',
            'alamat' => 'Tasikmalaya',
        ]);

        // Kelas
        $kelas7A = Kelas::create([
            'nama_kelas' => '7A',
            'guru_id' => $guru->id,
        ]);

        $kelas8A = Kelas::create([
            'nama_kelas' => '8A',
            'guru_id' => null,
        ]);

        // Orang Tua
        $ot1 = OrangTua::create([
            'nama_ayah' => 'Bapak Budi Santoso',
            'nama_ibu' => 'Ibu Rahmawati',
            'no_wa' => '081234567891',
            'alamat' => 'Jl. Al-Mutaqin No. 12',
        ]);

        $ot2 = OrangTua::create([
            'nama_ayah' => 'Bapak Hendra Wibowo',
            'nama_ibu' => 'Ibu Ani Lestari',
            'no_wa' => '081298765432',
            'alamat' => 'Jl. Perintis Kemerdekaan No. 45',
        ]);

        // Siswa
        Siswa::create([
            'nisn' => '0081234501',
            'nis' => '23241001',
            'nama' => 'Muhammad Rizky Pratama',
            'jenis_kelamin' => 'L',
            'kelas_id' => $kelas7A->id,
            'orang_tua_id' => $ot1->id,
            'qr_code_token' => 'SMPIT-0081234501-' . Str::random(4),
        ]);

        Siswa::create([
            'nisn' => '0081234502',
            'nis' => '23241002',
            'nama' => 'Aisyah Az-Zahra',
            'jenis_kelamin' => 'P',
            'kelas_id' => $kelas7A->id,
            'orang_tua_id' => $ot2->id,
            'qr_code_token' => 'SMPIT-0081234502-' . Str::random(4),
        ]);

        // Jam Presensi Standard
        JamPresensi::create([
            'nama_jadwal' => 'Jadwal Reguler Harian',
            'jam_masuk' => '07:00:00',
            'jam_terlambat' => '07:15:00',
            'jam_pulang' => '15:00:00',
            'is_active' => true,
        ]);

        // Setting Fonnte Initial
        SettingFonnte::create([
            'api_token' => '', // Silakan diisi via dashboard admin
            'template_masuk' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi SMP IT Al-Mutaqin:\nAnanda {nama} (Kelas {kelas}) telah tiba di sekolah dan melakukan presensi MASUK pada tanggal {tanggal} pukul {waktu}.\n\nTerima kasih.",
            'template_terlambat' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi TERLAMBAT SMP IT Al-Mutaqin:\nAnanda {nama} (Kelas {kelas}) melakukan presensi pada pukul {waktu} (TERLAMBAT).\nMohon bimbingan dari Bpk/Ibu Orang Tua.\n\nTerima kasih.",
            'template_pulang' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi PULANG SMP IT Al-Mutaqin:\nAnanda {nama} (Kelas {kelas}) telah melakukan presensi PULANG pada tanggal {tanggal} pukul {waktu}.\n\nTerima kasih.",
            'is_active' => true,
        ]);
    }
}
