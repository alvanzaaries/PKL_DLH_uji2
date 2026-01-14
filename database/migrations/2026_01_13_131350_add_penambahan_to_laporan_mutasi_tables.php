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
        // Tambah kolom penambahan_volume di laporan_mutasi_kayu_bulat
        Schema::table('laporan_mutasi_kayu_bulat', function (Blueprint $table) {
            $table->float('penambahan_volume')->default(0)->after('persediaan_awal_volume');
        });

        // Tambah kolom penambahan_volume di laporan_mutasi_kayu_olahan
        Schema::table('laporan_mutasi_kayu_olahan', function (Blueprint $table) {
            $table->float('penambahan_volume')->default(0)->after('persediaan_awal_volume');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_mutasi_kayu_bulat', function (Blueprint $table) {
            $table->dropColumn('penambahan_volume');
        });

        Schema::table('laporan_mutasi_kayu_olahan', function (Blueprint $table) {
            $table->dropColumn('penambahan_volume');
        });
    }
};
