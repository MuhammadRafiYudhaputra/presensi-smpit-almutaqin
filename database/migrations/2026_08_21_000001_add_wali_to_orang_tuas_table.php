<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orang_tuas', function (Blueprint $table) {
            $table->string('nama_wali')->nullable()->after('nama_ibu');
            $table->string('hubungan_wali')->nullable()->after('nama_wali'); // Contoh: Kakek/Nenek, Paman/Bibi, Wali Asuh
        });
    }

    public function down(): void
    {
        Schema::table('orang_tuas', function (Blueprint $table) {
            $table->dropColumn(['nama_wali', 'hubungan_wali']);
        });
    }
};
