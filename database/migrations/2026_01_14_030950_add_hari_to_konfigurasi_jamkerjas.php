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

            // jam kerja (boleh null jika libur)
            $table->foreignId('jam_kerja_id')
                ->nullable()
                ->change();

            // status libur
            $table->boolean('libur')
                ->default(false)
                ->after('jam_kerja_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('konfigurasi_jamkerjas', function (Blueprint $table) {
            $table->dropColumn(['libur']);
        });
    }
};
