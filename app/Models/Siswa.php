<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;

    protected $fillable = [
        'nisn',
        'nis',
        'nama',
        'jenis_kelamin',
        'kelas_id',
        'orang_tua_id',
        'qr_code_token',
        'status',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function orangTua()
    {
        return $this->belongsTo(OrangTua::class, 'orang_tua_id');
    }

    public function kehadirans()
    {
        return $this->hasMany(Kehadiran::class, 'siswa_id');
    }

    public function riwayatKelas()
    {
        return $this->hasMany(RiwayatKelas::class, 'siswa_id');
    }

    /**
     * Dapatkan kelas siswa pada tahun ajaran tertentu
     */
    public function getKelasForTahunAjaran($tahunAjaran)
    {
        $riwayat = $this->riwayatKelas()->where('tahun_ajaran', $tahunAjaran)->first();
        if ($riwayat && $riwayat->kelas) {
            return $riwayat->kelas;
        }

        return $this->kelas;
    }
}
