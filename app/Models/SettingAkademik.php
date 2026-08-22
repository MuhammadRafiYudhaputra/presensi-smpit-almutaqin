<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingAkademik extends Model
{
    use HasFactory;

    protected $table = 'setting_akademiks';

    protected $fillable = [
        'tahun_ajaran',
        'semester',
        'is_active',
    ];

    /**
     * Dapatkan setting akademik yang sedang aktif
     */
    public static function getActive()
    {
        return static::where('is_active', true)->latest()->first() ?? static::firstOrCreate(
            ['is_active' => true],
            [
                'tahun_ajaran' => '2026/2027',
                'semester' => 'ganjil',
            ]
        );
    }
}
