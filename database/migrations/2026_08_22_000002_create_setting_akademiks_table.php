<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_akademiks', function (Blueprint $table) {
            $table->id();
            $table->string('tahun_ajaran', 20)->default('2026/2027');
            $table->enum('semester', ['ganjil', 'genap'])->default('ganjil');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Insert default record
        \DB::table('setting_akademiks')->insert([
            'tahun_ajaran' => '2026/2027',
            'semester' => 'ganjil',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_akademiks');
    }
};
