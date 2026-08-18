<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('setting_fonntes', function (Blueprint $table) {
            $table->id();
            $table->string('api_token')->nullable();
            $table->text('template_masuk')->nullable();
            $table->text('template_terlambat')->nullable();
            $table->text('template_pulang')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('setting_fonntes');
    }
};
