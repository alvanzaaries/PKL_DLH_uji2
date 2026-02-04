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
        Schema::table('industri_sekunder', function (Blueprint $table) {
            $table->integer('total_nilai_investasi')->nullable()->after('kapasitas_izin')->comment('Total nilai investasi dalam rupiah');
            $table->integer('total_pegawai')->nullable()->after('total_nilai_investasi')->comment('Total jumlah pegawai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('industri_sekunder', function (Blueprint $table) {
            $table->dropColumn(['total_nilai_investasi', 'total_pegawai']);
        });
    }
};
