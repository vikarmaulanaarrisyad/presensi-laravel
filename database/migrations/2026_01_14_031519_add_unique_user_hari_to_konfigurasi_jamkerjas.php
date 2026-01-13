<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('konfigurasi_jamkerjas', function (Blueprint $table) {
            $table->unique(['user_id', 'hari'], 'konfigurasi_jamkerja_user_hari_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konfigurasi_jamkerjas', function (Blueprint $table) {
            $table->dropUnique('konfigurasi_jamkerja_user_hari_unique');
        });
    }
};
