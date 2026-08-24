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
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin TU User
        User::updateOrCreate(
            ['email' => 'admin@almutaqin.sch.id'],
            [
                'name' => 'Admin TU SMP IT Al-Muttaqin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // 2. Guru User & Data Guru
        $userGuru1 = User::updateOrCreate(
            ['email' => 'guru@almutaqin.sch.id'],
            [
                'name' => 'Ustadz Ahmad, S.Pd',
                'password' => Hash::make('12345678'),
                'role' => 'guru',
            ]
        );

        $guru1 = Guru::updateOrCreate(
            ['nip' => '198501012010011001'],
            [
                'user_id' => $userGuru1->id,
                'nama' => 'Ustadz Ahmad, S.Pd',
                'no_hp' => '081234567890',
                'alamat' => 'Tasikmalaya',
            ]
        );

        $userGuru2 = User::updateOrCreate(
            ['email' => 'fatimah@almuttaqin.sch.id'],
            [
                'name' => 'Ustadzah Fatimah, S.Pd',
                'password' => Hash::make('12345678'),
                'role' => 'guru',
            ]
        );

        $guru2 = Guru::updateOrCreate(
            ['nip' => '198803152012022002'],
            [
                'user_id' => $userGuru2->id,
                'nama' => 'Ustadzah Fatimah, S.Pd',
                'no_hp' => '081987654321',
                'alamat' => 'Garut',
            ]
        );

        // 3. Exact 4 Classes: Kelas 7, Kelas 8, Kelas 9A, Kelas 9B
        $k7 = Kelas::updateOrCreate(['nama_kelas' => '7'], ['guru_id' => $guru1->id]);
        $k8 = Kelas::updateOrCreate(['nama_kelas' => '8'], ['guru_id' => $guru2->id]);
        $k9A = Kelas::updateOrCreate(['nama_kelas' => '9A'], ['guru_id' => null]);
        $k9B = Kelas::updateOrCreate(['nama_kelas' => '9B'], ['guru_id' => null]);

        // Bersihkan nama kelas selain 7, 8, 9A, 9B jika ada
        Kelas::whereNotIn('nama_kelas', ['7', '8', '9A', '9B'])->delete();

        // 4. Jam Presensi Standar
        JamPresensi::updateOrCreate(
            ['nama_jadwal' => 'Jadwal Reguler Sekolah'],
            [
                'jam_masuk' => '07:00:00',
                'jam_terlambat' => '07:10:00',
                'jam_pulang' => '14:30:00',
                'is_active' => true,
            ]
        );

        // 7. Setting Fonnte Initial
        SettingFonnte::updateOrCreate(
            ['id' => 1],
            [
                'api_token' => 'YOUR_FONNTE_TOKEN_HERE',
                'template_masuk' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi MASUK SMP IT Al-Muttaqin:\nNama: {nama}\nNISN: {nisn}\nKelas: {kelas}\nStatus: {status}\n\nTelah melakukan presensi MASUK pada tanggal {tanggal} pukul {waktu}.\n\nTerima kasih.",
                'template_terlambat' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi TERLAMBAT SMP IT Al-Muttaqin:\nNama: {nama}\nNISN: {nisn}\nKelas: {kelas}\nStatus: {status}\n\nTelah melakukan presensi MASUK TERLAMBAT pada tanggal {tanggal} pukul {waktu}. Mohon perhatian dari Bapak/Ibu Wali Murid.\n\nTerima kasih.",
                'template_pulang' => "Assalamu'alaikum Warahmatullahi Wabarakatuh.\n\nPemberitahuan Presensi PULANG SMP IT Al-Muttaqin:\nNama: {nama}\nNISN: {nisn}\nKelas: {kelas}\n\nTelah menyelesaikan kegiatan belajar dan melakukan presensi PULANG pada tanggal {tanggal} pukul {waktu}.\n\nTerima kasih.",
                'is_active' => true,
            ]
        );

        // 8. Generate 112 Siswa Lengkap, Orang Tua, Riwayat Kelas Historis & Sample Kehadiran
        $this->call(DummySchoolDataSeeder::class);
    }
}
