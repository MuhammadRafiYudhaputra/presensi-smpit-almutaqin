<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('riwayat_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('siswa_id')->constrained('siswas')->onDelete('cascade');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->string('tahun_ajaran', 20); // contoh: '2024/2025', '2025/2026', '2026/2027'
            $table->enum('status', ['aktif', 'naik_kelas', 'lulus', 'tinggal_kelas'])->default('aktif');
            $table->timestamps();

            // Index untuk kecepatan query rekapitulasi per tahun ajaran
            $table->index(['siswa_id', 'tahun_ajaran']);
            $table->index(['kelas_id', 'tahun_ajaran']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('riwayat_kelas');
    }
};
