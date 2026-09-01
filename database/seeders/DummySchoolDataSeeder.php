<?php

namespace Database\Seeders;

use App\Models\Guru;
use App\Models\Kelas;
use App\Models\OrangTua;
use App\Models\Siswa;
use App\Models\RiwayatKelas;
use App\Models\Kehadiran;
use App\Models\SettingAkademik;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DummySchoolDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan Kelas & Wali Kelas Terdaftar
        $guru1 = Guru::first();
        $guru2 = Guru::skip(1)->first();

        $k7 = Kelas::updateOrCreate(['nama_kelas' => '7'], ['guru_id' => $guru1 ? $guru1->id : null]);
        $k8 = Kelas::updateOrCreate(['nama_kelas' => '8'], ['guru_id' => $guru2 ? $guru2->id : null]);
        $k9A = Kelas::updateOrCreate(['nama_kelas' => '9A'], ['guru_id' => null]);
        $k9B = Kelas::updateOrCreate(['nama_kelas' => '9B'], ['guru_id' => null]);

        // Daftar Nama Lengkap Siswa Realistis & Islami
        // Kelas 7: 32 Siswa (Masuk T.A 2025/2026, NIS: 25261001 - 25261032, NISN: 012...)
        $dataKelas7 = [
            ['nama' => 'Muhammad Rizky Pratama', 'jk' => 'L', 'ayah' => 'Budi Santoso', 'ibu' => 'Rahmawati'],
            ['nama' => 'Ahmad Fadhil Al-Farizi', 'jk' => 'L', 'ayah' => 'Hendra Gunawan', 'ibu' => 'Siti Maryam'],
            ['nama' => 'Alif Nur Hidayat', 'jk' => 'L', 'ayah' => 'Dadan Ramdani', 'ibu' => 'Enok Nurjanah'],
            ['nama' => 'Bilal Habibi Mustofa', 'jk' => 'L', 'ayah' => 'Cecep Supriatna', 'ibu' => 'Ai Kurniasih'],
            ['nama' => 'Dimas Arya Permana', 'jk' => 'L', 'ayah' => 'Asep Saepuloh', 'ibu' => 'Rina Marlina'],
            ['nama' => 'Faris Zaidan Akmal', 'jk' => 'L', 'ayah' => 'Iwan Kurniawan', 'ibu' => 'Dewi Sartika'],
            ['nama' => 'Habibi Syauqi Rahman', 'jk' => 'L', 'ayah' => 'Agus Salim', 'ibu' => 'Neng Fatimah'],
            ['nama' => 'Ihsan Kamil Al-Ghifari', 'jk' => 'L', 'ayah' => 'Yana Mulyana', 'ibu' => 'Imas Masitoh'],
            ['nama' => 'Kenzi Alvaro Ramadhan', 'jk' => 'L', 'ayah' => 'Wawan Setiawan', 'ibu' => 'Tuti Alawiyah'],
            ['nama' => 'Lutfi Zaki An-Nafis', 'jk' => 'L', 'ayah' => 'Nanang Suherman', 'ibu' => 'Lia Dahlia'],
            ['nama' => 'Miftah Fauzan Hakim', 'jk' => 'L', 'ayah' => 'Ujang Syarif', 'ibu' => 'Elis Rosita'],
            ['nama' => 'Naufal Raihan Akbar', 'jk' => 'L', 'ayah' => 'Ahmad Sobari', 'ibu' => 'Ani Nurhayani'],
            ['nama' => 'Rafi Azhar Pratama', 'jk' => 'L', 'ayah' => 'Dadang Hidayat', 'ibu' => 'Nurul Aini'],
            ['nama' => 'Salman Al-Farisi', 'jk' => 'L', 'ayah' => 'Aep Saepudin', 'ibu' => 'Euis Komalasari'],
            ['nama' => 'Taufiqurrahman Hakim', 'jk' => 'L', 'ayah' => 'Jajang Nurjaman', 'ibu' => 'Kokom Komariah'],
            ['nama' => 'Zayyan Khalid Al-Mubarok', 'jk' => 'L', 'ayah' => 'Toto Sugiarto', 'ibu' => 'Sri Mulyani'],
            ['nama' => 'Aisyah Putri Azzahra', 'jk' => 'P', 'ayah' => 'Ade Mulyadi', 'ibu' => 'Yuyun Yuningsih'],
            ['nama' => 'Anisa Rahmawati', 'jk' => 'P', 'ayah' => 'Ade Suherman', 'ibu' => 'Ratna Juwita'],
            ['nama' => 'Aqila Humaira Zahra', 'jk' => 'P', 'ayah' => 'Dedi Junaedi', 'ibu' => 'Siti Halimah'],
            ['nama' => 'Cahya Kamila Khairunnisa', 'jk' => 'P', 'ayah' => 'Eman Sulaeman', 'ibu' => 'Nani Sumarni'],
            ['nama' => 'Dafina Salsabila', 'jk' => 'P', 'ayah' => 'Gunawan Wibisono', 'ibu' => 'Neneng Hasanah'],
            ['nama' => 'Fatimah Az-Zahra', 'jk' => 'P', 'ayah' => 'Hasan Basri', 'ibu' => 'Rohanah'],
            ['nama' => 'Hafidzah Nur Azizah', 'jk' => 'P', 'ayah' => 'Ikin Sodikin', 'ibu' => 'Nunung Nurjanah'],
            ['nama' => 'Kayla Zhafira Nabila', 'jk' => 'P', 'ayah' => 'Joni Iskandar', 'ibu' => 'Lilis Suryani'],
            ['nama' => 'Latifah Khairina', 'jk' => 'P', 'ayah' => 'Kosasih', 'ibu' => 'Heni Hendrayani'],
            ['nama' => 'Medina Althafunnisa', 'jk' => 'P', 'ayah' => 'Lukmanul Hakim', 'ibu' => 'Cucun Sunarsih'],
            ['nama' => 'Nada Syakira Putri', 'jk' => 'P', 'ayah' => 'Maman Sulaeman', 'ibu' => 'Popon Maryani'],
            ['nama' => 'Nayla Safitri Az-Zahra', 'jk' => 'P', 'ayah' => 'Oman Rohman', 'ibu' => 'Yanti Susanti'],
            ['nama' => 'Qonita Mufidah', 'jk' => 'P', 'ayah' => 'Pipin Supriatna', 'ibu' => 'Rina Rusmiati'],
            ['nama' => 'Rania Zahra Al-Munawwarah', 'jk' => 'P', 'ayah' => 'Rahmat Hidayat', 'ibu' => 'Wiwin Winarti'],
            ['nama' => 'Salma Tazkiya Aulia', 'jk' => 'P', 'ayah' => 'Syarif Hidayatulloh', 'ibu' => 'Yayah Rodiah'],
            ['nama' => 'Zaskia Nur Fadilah', 'jk' => 'P', 'ayah' => 'Teten Rustandi', 'ibu' => 'Zubaedah'],
        ];

        // Kelas 8: 40 Siswa (Masuk T.A 2024/2025, NIS: 24251001 - 24251040, NISN: 011...)
        $dataKelas8 = [
            ['nama' => 'Aditya Bagus Pratama', 'jk' => 'L', 'ayah' => 'Agus Suparman', 'ibu' => 'Tuti Handayani'],
            ['nama' => 'Akbar Maulana Malik', 'jk' => 'L', 'ayah' => 'Bambang Irawan', 'ibu' => 'Siti Julaeha'],
            ['nama' => 'Alwan Daffa Ramadhan', 'jk' => 'L', 'ayah' => 'Cahyadi', 'ibu' => 'Neng Lilis'],
            ['nama' => 'Arkananta Fadhil Jaya', 'jk' => 'L', 'ayah' => 'Didi Rosadi', 'ibu' => 'Endah Nurhayati'],
            ['nama' => 'Arya Bima Sena', 'jk' => 'L', 'ayah' => 'Eka Suherlan', 'ibu' => 'Yuyun Wahyuni'],
            ['nama' => 'Azka Danendra Putra', 'jk' => 'L', 'ayah' => 'Fajar Sidik', 'ibu' => 'Rina Astuti'],
            ['nama' => 'Bagas Maulana Ibrahim', 'jk' => 'L', 'ayah' => 'Ginanjar', 'ibu' => 'Siti Khodijah'],
            ['nama' => 'Danial Hafizh Al-Bantani', 'jk' => 'L', 'ayah' => 'Hadi Purnomo', 'ibu' => 'Iis Ismawati'],
            ['nama' => 'Fadlan Syamil Hakim', 'jk' => 'L', 'ayah' => 'Iskandar Zulkarnain', 'ibu' => 'Ai Rohimah'],
            ['nama' => 'Galih Rakha Satria', 'jk' => 'L', 'ayah' => 'Jajang Hidayat', 'ibu' => 'Tini Suhartini'],
            ['nama' => 'Haidar Ali Al-Ghazi', 'jk' => 'L', 'ayah' => 'Kurniawan', 'ibu' => 'Nia Kania'],
            ['nama' => 'Ilham Maulana Syahputra', 'jk' => 'L', 'ayah' => 'Lili Somantri', 'ibu' => 'Lilis Rosita'],
            ['nama' => 'Irfan Syahrizal', 'jk' => 'L', 'ayah' => 'Mulyana', 'ibu' => 'Ema Rahmawati'],
            ['nama' => 'Kevin Al-Farizi', 'jk' => 'L', 'ayah' => 'Nurdin', 'ibu' => 'Yani Mulyani'],
            ['nama' => 'M. Fathan Mubarok', 'jk' => 'L', 'ayah' => 'Oding Komarudin', 'ibu' => 'Eni Nuraeni'],
            ['nama' => 'M. Zidan Al-Ghani', 'jk' => 'L', 'ayah' => 'Purnama', 'ibu' => 'Wiwin Widaningsih'],
            ['nama' => 'Nabil Ahmad Shiddiq', 'jk' => 'L', 'ayah' => 'Qomarudin', 'ibu' => 'Ai Nurhasanah'],
            ['nama' => 'Raditya Arya Nugraha', 'jk' => 'L', 'ayah' => 'Rudi Hartono', 'ibu' => 'Siti Saadah'],
            ['nama' => 'Rifqi Fauzan Azhari', 'jk' => 'L', 'ayah' => 'Sopian', 'ibu' => 'Tati Rohayati'],
            ['nama' => 'Zikri Maulana Malik', 'jk' => 'L', 'ayah' => 'Taufik Ismail', 'ibu' => 'Ucu Sumarni'],
            ['nama' => 'Alya Nabila Syakira', 'jk' => 'P', 'ayah' => 'Undang Suhendar', 'ibu' => 'Vina Panduwinata'],
            ['nama' => 'Annisa Dwi Lestari', 'jk' => 'P', 'ayah' => 'Wahyu Hidayat', 'ibu' => 'Wati Kurniawati'],
            ['nama' => 'Aura Syifa Al-Khansa', 'jk' => 'P', 'ayah' => 'Yayat Ruhiyat', 'ibu' => 'Yanti Heryanti'],
            ['nama' => 'Bella Safira Maulida', 'jk' => 'P', 'ayah' => 'Zainal Abidin', 'ibu' => 'Zuraida'],
            ['nama' => 'Citra Dewi Sartika', 'jk' => 'P', 'ayah' => 'Aman', 'ibu' => 'Ai Komariah'],
            ['nama' => 'Dhea Amanda Putri', 'jk' => 'P', 'ayah' => 'Burhanudin', 'ibu' => 'Beti Nurbaeti'],
            ['nama' => 'Elsa Fitriani', 'jk' => 'P', 'ayah' => 'Cucu Suryana', 'ibu' => 'Cicih'],
            ['nama' => 'Farah Nida Nafisah', 'jk' => 'P', 'ayah' => 'Deden Kurnia', 'ibu' => 'Siti Nurhaliza'],
            ['nama' => 'Ghina Salsabila Mufidah', 'jk' => 'P', 'ayah' => 'Endang Sunandar', 'ibu' => 'Erna Herawati'],
            ['nama' => 'Hanifah Fauziah', 'jk' => 'P', 'ayah' => 'Firman Utina', 'ibu' => 'Fitriani'],
            ['nama' => 'Intan Nurul Aulia', 'jk' => 'P', 'ayah' => 'Gugun Gunawan', 'ibu' => 'Gita Gutawa'],
            ['nama' => 'Jihan Talita Zahra', 'jk' => 'P', 'ayah' => 'Heri Suheri', 'ibu' => 'Herna Hernawati'],
            ['nama' => 'Khadijah Al-Kubro', 'jk' => 'P', 'ayah' => 'Indra Lesmana', 'ibu' => 'Irma Suryani'],
            ['nama' => 'Lina Marlina', 'jk' => 'P', 'ayah' => 'Jaka Tarub', 'ibu' => 'Juariah'],
            ['nama' => 'Marwah Siti Rohmah', 'jk' => 'P', 'ayah' => 'Kusnadi', 'ibu' => 'Karmila'],
            ['nama' => 'Nabila Syakira', 'jk' => 'P', 'ayah' => 'Lukman Santoso', 'ibu' => 'Lela Nurlela'],
            ['nama' => 'Putri Ayu Wandira', 'jk' => 'P', 'ayah' => 'Muchtar', 'ibu' => 'Mimi Jamilah'],
            ['nama' => 'Rasyida Humaira', 'jk' => 'P', 'ayah' => 'Nurjaman', 'ibu' => 'Nengsih'],
            ['nama' => 'Syifa Fauziyyah', 'jk' => 'P', 'ayah' => 'Oom Somantri', 'ibu' => 'Opi Sofiah'],
            ['nama' => 'Zahra Khairunnisa', 'jk' => 'P', 'ayah' => 'Pepen Supendi', 'ibu' => 'Pipit Pitriani'],
        ];

        // Kelas 9A: 20 Siswa (Masuk T.A 2023/2024, NIS: 23241001 - 23241020, NISN: 010...)
        $dataKelas9A = [
            ['nama' => 'Ahmad Fauzi Ramadhan', 'jk' => 'L', 'ayah' => 'Deden Kurnia', 'ibu' => 'Siti Nurhaliza'],
            ['nama' => 'Alvaro Dwi Pratama', 'jk' => 'L', 'ayah' => 'Rahmat Hidayat', 'ibu' => 'Ai Sumarni'],
            ['nama' => 'Bintang Ramadhan Putra', 'jk' => 'L', 'ayah' => 'Surya Kencana', 'ibu' => 'Dian Anggraeni'],
            ['nama' => 'Daffa Maulana Akbar', 'jk' => 'L', 'ayah' => 'Yusuf Mansur', 'ibu' => 'Neni Triana'],
            ['nama' => 'Farhan Hakim Al-Ghifari', 'jk' => 'L', 'ayah' => 'Zulhasnan', 'ibu' => 'Haryati'],
            ['nama' => 'Gilang Arya Pratama', 'jk' => 'L', 'ayah' => 'Anwar Sanusi', 'ibu' => 'Enung Nurhayati'],
            ['nama' => 'Hafizh Al-Muqoddas', 'jk' => 'L', 'ayah' => 'Bahrudin', 'ibu' => 'Siti Aisyah'],
            ['nama' => 'Ikhwanul Muslimin', 'jk' => 'L', 'ayah' => 'Cepi Supriadi', 'ibu' => 'Ai Ratnasari'],
            ['nama' => 'M. Danial Rabbani', 'jk' => 'L', 'ayah' => 'Diki Wahyudi', 'ibu' => 'Sari Indah'],
            ['nama' => 'Naufal Dzakwan Putra', 'jk' => 'L', 'ayah' => 'Eman Sulaeman', 'ibu' => 'Tati Sumiati'],
            ['nama' => 'Siti Nur Aini', 'jk' => 'P', 'ayah' => 'Budi Santoso', 'ibu' => 'Rahmawati'],
            ['nama' => 'Adinda Zahra Maulida', 'jk' => 'P', 'ayah' => 'Fahmi Idris', 'ibu' => 'Nurul Huda'],
            ['nama' => 'Chika Al-Zahra', 'jk' => 'P', 'ayah' => 'Gani Supriatna', 'ibu' => 'Lilis Rosita'],
            ['nama' => 'Dinda Syifa Khairina', 'jk' => 'P', 'ayah' => 'Hendri Hendrawan', 'ibu' => 'Ira Maya'],
            ['nama' => 'Felia Anggraeni Putri', 'jk' => 'P', 'ayah' => 'Iman Budiman', 'ibu' => 'Yeni Mulyani'],
            ['nama' => 'Hana Salsabila', 'jk' => 'P', 'ayah' => 'Joni Iskandar', 'ibu' => 'Lina Herlina'],
            ['nama' => 'Keisha Az-Zahra', 'jk' => 'P', 'ayah' => 'Kiki Kurnia', 'ibu' => 'Nani Nurjanah'],
            ['nama' => 'Lutfiah Nur Azizah', 'jk' => 'P', 'ayah' => 'Latif Mubarok', 'ibu' => 'Oka Rostika'],
            ['nama' => 'Najwa Shihab Putri', 'jk' => 'P', 'ayah' => 'Muhtarom', 'ibu' => 'Popi Sopiah'],
            ['nama' => 'Ratu Bilqis Ufairah', 'jk' => 'P', 'ayah' => 'Nazarudin', 'ibu' => 'Rini Rosmini'],
        ];

        // Kelas 9B: 20 Siswa (Masuk T.A 2023/2024, NIS: 23241021 - 23241040, NISN: 010...)
        $dataKelas9B = [
            ['nama' => 'Andra Pratama Wijaya', 'jk' => 'L', 'ayah' => 'Oka Wijaya', 'ibu' => 'Sri Rahayu'],
            ['nama' => 'Bima Sakti Al-Farizi', 'jk' => 'L', 'ayah' => 'Panji Gumilang', 'ibu' => 'Titik Puspa'],
            ['nama' => 'Candra Wijaya Kusuma', 'jk' => 'L', 'ayah' => 'Qodir Jaelani', 'ibu' => 'Uum Umayah'],
            ['nama' => 'Dzaki Al-Mubarok', 'jk' => 'L', 'ayah' => 'Rudi Tabuti', 'ibu' => 'Vera Verawati'],
            ['nama' => 'Fajar Shiddiq Rahman', 'jk' => 'L', 'ayah' => 'Samsul Bahri', 'ibu' => 'Wina Winarti'],
            ['nama' => 'Gibran Rakabuming Putra', 'jk' => 'L', 'ayah' => 'Tri Sutrisno', 'ibu' => 'Yuyun Sukaesih'],
            ['nama' => 'Helmi Faishal Syah', 'jk' => 'L', 'ayah' => 'Usman Harun', 'ibu' => 'Zahra Latifah'],
            ['nama' => 'Iqbal Ramadhan Syah', 'jk' => 'L', 'ayah' => 'Viktor Simamora', 'ibu' => 'Ai Hernawati'],
            ['nama' => 'M. Zaidan Al-Farisi', 'jk' => 'L', 'ayah' => 'Wahyu Hidayat', 'ibu' => 'Beti Nurbaeti'],
            ['nama' => 'Reza Pahlevi Akbar', 'jk' => 'L', 'ayah' => 'Yudi Guntara', 'ibu' => 'Cucu Suryani'],
            ['nama' => 'Aisyah Az-Zahra Al-Bantani', 'jk' => 'P', 'ayah' => 'Hendra Wibowo', 'ibu' => 'Ani Lestari'],
            ['nama' => 'Bunga Citra Lestari', 'jk' => 'P', 'ayah' => 'Zulkifli Lubis', 'ibu' => 'Dedeh Rosidah'],
            ['nama' => 'Dwi Astuti Handayani', 'jk' => 'P', 'ayah' => 'Agus Kuncoro', 'ibu' => 'Erni Herawati'],
            ['nama' => 'Fitria Ramadhani', 'jk' => 'P', 'ayah' => 'Budi Sudarsono', 'ibu' => 'Fani Safitri'],
            ['nama' => 'Gita Gutawa Putri', 'jk' => 'P', 'ayah' => 'Cecep Reza', 'ibu' => 'Gita Gutawa'],
            ['nama' => 'Intan Permata Sari', 'jk' => 'P', 'ayah' => 'Dedi Mizwar', 'ibu' => 'Heni Hendrayani'],
            ['nama' => 'Karina Aulia Putri', 'jk' => 'P', 'ayah' => 'Eko Patrio', 'ibu' => 'Irma Darmawangsa'],
            ['nama' => 'Maudy Ayunda Zahra', 'jk' => 'P', 'ayah' => 'Ferry Maryadi', 'ibu' => 'Juwita Bahar'],
            ['nama' => 'Nissa Sabyan Aulia', 'jk' => 'P', 'ayah' => 'Gading Marten', 'ibu' => 'Kartika Putri'],
            ['nama' => 'Zahra Amelia Putri', 'jk' => 'P', 'ayah' => 'Hari Darmawan', 'ibu' => 'Lulu Tobing'],
        ];

        $alamatSample = [
            'Kp. Sirah Cijugul RT 02/RW 04, Panjiwangi, Tarogong Kaler',
            'Jl. Pembangunan No. 88 RT 03/RW 02 Sukagalih, Tarogong Kidul',
            'Kp. Citaman RT 01/RW 07 Panjiwangi, Tarogong Kaler',
            'Perum Cempaka Indah Blok B-12, Karangpawitan',
            'Kp. Babakan Abid RT 04/RW 01, Samarang',
            'Jl. Raya Samarang No. 102, Tarogong Kidul',
            'Kp. Jati RT 03/RW 05, Pasawahan, Tarogong Kaler',
            'Perumahan Griya Surya Kencana Blok C-05, Garut Kota',
        ];

        // Seed Siswa Generator
        $validNisns = [];
        $validNisns = array_merge($validNisns, $this->seedBatchStudents($dataKelas7, $k7, '2526', '012', '2025/2026', 1, $alamatSample, ['2025/2026' => $k7->id]));
        $validNisns = array_merge($validNisns, $this->seedBatchStudents($dataKelas8, $k8, '2425', '011', '2024/2025', 1, $alamatSample, ['2024/2025' => $k7->id, '2025/2026' => $k8->id]));
        $validNisns = array_merge($validNisns, $this->seedBatchStudents($dataKelas9A, $k9A, '2324', '010', '2023/2024', 1, $alamatSample, ['2023/2024' => $k7->id, '2024/2025' => $k8->id, '2025/2026' => $k9A->id]));
        $validNisns = array_merge($validNisns, $this->seedBatchStudents($dataKelas9B, $k9B, '2324', '010', '2023/2024', 21, $alamatSample, ['2023/2024' => $k7->id, '2024/2025' => $k8->id, '2025/2026' => $k9B->id]));

        // Bersihkan data siswa di database yang bukan bagian dari 112 siswa resmi secara aman (cascade relation)
        $invalidSiswaIds = Siswa::whereNotIn('nisn', $validNisns)->pluck('id');
        if ($invalidSiswaIds->isNotEmpty()) {
            Kehadiran::whereIn('siswa_id', $invalidSiswaIds)->delete();
            RiwayatKelas::whereIn('siswa_id', $invalidSiswaIds)->delete();
            Siswa::whereIn('id', $invalidSiswaIds)->delete();
        }

        // Bersihkan data orang tua yang tidak memiliki siswa terkait
        OrangTua::doesntHave('siswas')->delete();

        // 5. Pastikan Setting Periode Akademik Aktif adalah 2026/2027 Ganjil
        SettingAkademik::query()->update(['is_active' => false]);
        SettingAkademik::updateOrCreate(
            ['tahun_ajaran' => '2026/2027', 'semester' => 'ganjil'],
            ['is_active' => true]
        );

        // Seed Sample Kehadiran untuk 14 Hari Terakhir (Agar grafik dashboard & rekap penuh & realistis)
        $this->seedAttendanceRecords();
    }

    private function seedBatchStudents(array $list, Kelas $currentKelas, string $nisPrefix, string $nisnPrefix, string $tahunMasuk, int $startCounter, array $alamatList, array $historyMap): array
    {
        $seededNisns = [];
        $counter = $startCounter;
        foreach ($list as $item) {
            $nis = $nisPrefix . str_pad($counter, 4, '0', STR_PAD_LEFT);
            $nisn = $nisnPrefix . str_pad($counter + 1000000, 7, '0', STR_PAD_LEFT);
            $seededNisns[] = $nisn;
            $counter++;

            // Orang Tua (No. WA Deterministik dan stabil)
            $phoneWa = '0823' . substr($nisn, -8);
            $fixedAlamat = $alamatList[($counter) % count($alamatList)];

            $ot = OrangTua::updateOrCreate(
                ['nama_ayah' => $item['ayah'], 'nama_ibu' => $item['ibu']],
                [
                    'no_wa' => $phoneWa,
                    'alamat' => $fixedAlamat,
                ]
            );

            // Token QR Code Konsisten & Statis Berdasarkan NISN
            $qrToken = 'SMPIT-' . $nisn;

            $siswa = Siswa::updateOrCreate(
                ['nisn' => $nisn],
                [
                    'nis' => $nis,
                    'nama' => $item['nama'],
                    'jenis_kelamin' => $item['jk'],
                    'kelas_id' => $currentKelas->id,
                    'orang_tua_id' => $ot->id,
                    'qr_code_token' => $qrToken,
                    'status' => 'aktif',
                ]
            );

            // Simpan Riwayat Kelas Historis Sesuai Tahun Ajaran Masuknya
            foreach ($historyMap as $ta => $kId) {
                RiwayatKelas::updateOrCreate(
                    ['siswa_id' => $siswa->id, 'tahun_ajaran' => $ta],
                    ['kelas_id' => $kId, 'status' => 'aktif']
                );
            }
        }

        return $seededNisns;
    }

    private function seedAttendanceRecords()
    {
        $siswas = Siswa::all();
        $today = Carbon::today();

        // Pastikan hari ini (Today) KOSONG / BELUM ABSEN agar siap untuk demo aplikasi
        Kehadiran::whereDate('tanggal', $today)->delete();

        // 14 hari efektif terakhir dimulai dari KEMARIN (H-1) ke belakang
        $dates = [];
        $cursor = $today->copy()->subDay();
        while (count($dates) < 14) {
            if (!$cursor->isWeekend() && !\App\Helpers\HolidayHelper::isNationalHoliday($cursor)) {
                $dates[] = $cursor->copy()->toDateString();
            }
            $cursor->subDay();
        }

        foreach ($siswas as $idx => $s) {
            foreach ($dates as $dIdx => $dateStr) {
                // Jangan buat kehadiran dobel jika sudah ada
                if (Kehadiran::where('siswa_id', $s->id)->where('tanggal', $dateStr)->exists()) {
                    continue;
                }

                // Pola kehadiran realistis:
                // 85% Hadir tepat waktu, 8% Terlambat, 4% Izin, 2% Sakit, 1% Alpa
                $rand = rand(1, 100);
                if ($rand <= 82) {
                    $status = 'HADIR';
                    $jamMasuk = sprintf('06:%02d:%02d', rand(40, 59), rand(10, 59));
                    $jamPulang = sprintf('14:%02d:%02d', rand(30, 55), rand(10, 59));
                } elseif ($rand <= 92) {
                    $status = 'TERLAMBAT';
                    $jamMasuk = sprintf('07:%02d:%02d', rand(11, 28), rand(10, 59));
                    $jamPulang = sprintf('14:%02d:%02d', rand(30, 55), rand(10, 59));
                } elseif ($rand <= 96) {
                    $status = 'IZIN';
                    $jamMasuk = null;
                    $jamPulang = null;
                } elseif ($rand <= 99) {
                    $status = 'SAKIT';
                    $jamMasuk = null;
                    $jamPulang = null;
                } else {
                    $status = 'ALPA';
                    $jamMasuk = null;
                    $jamPulang = null;
                }

                Kehadiran::create([
                    'siswa_id' => $s->id,
                    'tanggal' => $dateStr,
                    'jam_masuk' => $jamMasuk,
                    'jam_pulang' => $jamPulang,
                    'status' => $status,
                    'wa_masuk_sent' => true,
                    'wa_pulang_sent' => ($jamPulang !== null),
                ]);
            }
        }

        // 6. Seed Default Hari Efektif per Kelas untuk Kalender Akademik
        $kelasMap = [
            '7' => ['ganjil' => 110, 'genap' => 110, 'bulan' => 22],
            '8' => ['ganjil' => 110, 'genap' => 110, 'bulan' => 22],
            '9A' => ['ganjil' => 110, 'genap' => 85, 'bulan' => 20],
            '9B' => ['ganjil' => 110, 'genap' => 85, 'bulan' => 20],
        ];

        $kelasObjMap = [
            '7' => Kelas::where('nama_kelas', '7')->first(),
            '8' => Kelas::where('nama_kelas', '8')->first(),
            '9A' => Kelas::where('nama_kelas', '9A')->first(),
            '9B' => Kelas::where('nama_kelas', '9B')->first(),
        ];

        $academicYears = ['2025/2026', '2026/2027'];
        foreach ($academicYears as $ta) {
            $baseYear = (int) substr($ta, 0, 4);
            foreach ($kelasMap as $kName => $cfg) {
                $kls = $kelasObjMap[$kName] ?? null;
                if (!$kls) continue;

                // Mode Semester Ganjil
                \App\Models\HariEfektif::updateOrCreate([
                    'tahun_ajaran' => $ta,
                    'semester' => 'ganjil',
                    'mode' => 'semester',
                    'tahun' => $baseYear,
                    'kelas_id' => $kls->id,
                ], [
                    'jumlah_hari' => $cfg['ganjil'],
                ]);

                // Mode Semester Genap
                \App\Models\HariEfektif::updateOrCreate([
                    'tahun_ajaran' => $ta,
                    'semester' => 'genap',
                    'mode' => 'semester',
                    'tahun' => $baseYear + 1,
                    'kelas_id' => $kls->id,
                ], [
                    'jumlah_hari' => $cfg['genap'],
                ]);

                // Mode Bulanan (Sample untuk semua 12 bulan)
                for ($m = 1; $m <= 12; $m++) {
                    $yr = ($m >= 7) ? $baseYear : ($baseYear + 1);
                    $sem = ($m >= 7) ? 'ganjil' : 'genap';
                    $targetHari = ($kName === '9A' || $kName === '9B') && in_array($m, [4, 5, 6]) ? 16 : 22;

                    \App\Models\HariEfektif::updateOrCreate([
                        'tahun_ajaran' => $ta,
                        'semester' => $sem,
                        'mode' => 'bulanan',
                        'bulan' => $m,
                        'tahun' => $yr,
                        'kelas_id' => $kls->id,
                    ], [
                        'jumlah_hari' => $targetHari,
                    ]);
                }
            }
        }
    }
}
