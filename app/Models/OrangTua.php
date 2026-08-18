<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrangTua extends Model
{
    use HasFactory;

    protected $table = 'orang_tuas';

    protected $fillable = [
        'nama_ayah',
        'nama_ibu',
        'no_wa',
        'alamat',
    ];

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'orang_tua_id');
    }
}
