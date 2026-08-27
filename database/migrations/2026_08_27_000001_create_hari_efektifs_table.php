<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hari_efektifs', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran', 20); // e.g. 2026/2027
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->enum('mode', ['bulanan', 'semester'])->default('bulanan');
            $table->unsignedTinyInteger('bulan')->nullable(); // 1-12 (null if mode = semester)
            $table->unsignedSmallInteger('tahun'); // e.g. 2026
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->unsignedSmallInteger('jumlah_hari')->default(20);
            $table->string('keterangan')->nullable();
            $table->timestamps();

            $table->index(['tahun_ajaran', 'semester', 'mode', 'bulan', 'tahun', 'kelas_id'], 'idx_hari_efektif_lookup');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hari_efektifs');
    }
};
