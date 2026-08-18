<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SettingFonnte extends Model
{
    use HasFactory;

    protected $fillable = [
        'api_token',
        'template_masuk',
        'template_terlambat',
        'template_pulang',
        'is_active',
    ];
}
