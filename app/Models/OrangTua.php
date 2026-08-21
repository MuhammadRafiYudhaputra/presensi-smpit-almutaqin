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
        'nama_wali',
        'hubungan_wali',
        'no_wa',
        'alamat',
    ];

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'orang_tua_id');
    }

    /**
     * Get primary display name for parent / guardian
     */
    public function getNamaUtamaAttribute()
    {
        if (!empty($this->nama_ayah)) {
            return $this->nama_ayah . ' (Ayah)';
        }
        if (!empty($this->nama_ibu)) {
            return $this->nama_ibu . ' (Ibu)';
        }
        if (!empty($this->nama_wali)) {
            return $this->nama_wali . ($this->hubungan_wali ? " ({$this->hubungan_wali})" : ' (Wali)');
        }
        return 'Orang Tua / Wali';
    }
}
