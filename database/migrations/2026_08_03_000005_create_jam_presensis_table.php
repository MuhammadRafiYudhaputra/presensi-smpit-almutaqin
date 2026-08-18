<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_presensis', function (Blueprint $table) {
            $table->id();
            $table->string('nama_jadwal')->default('Reguler Harian');
            $table->time('jam_masuk')->default('07:00:00');
            $table->time('jam_terlambat')->default('07:15:00');
            $table->time('jam_pulang')->default('15:00:00');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_presensis');
    }
};
