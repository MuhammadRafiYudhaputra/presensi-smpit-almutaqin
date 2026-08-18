<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JamPresensi extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama_jadwal',
        'jam_masuk',
        'jam_terlambat',
        'jam_pulang',
        'is_active',
    ];
}
