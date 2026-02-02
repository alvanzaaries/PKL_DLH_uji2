<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Menambahkan kolom jumlah_btg untuk persediaan awal, penambahan,
     * pengurangan, dan persediaan akhir pada tabel mutasi kayu bulat dan olahan.
     */
    public function up(): void
    {
        // Tambah kolom jumlah_btg di laporan_mutasi_kayu_bulat
        Schema::table('laporan_mutasi_kayu_bulat', function (Blueprint $table) {
            $table->integer('persediaan_awal_btg')->default(0)->after('jenis_kayu');
            $table->integer('penambahan_btg')->default(0)->after('penambahan_volume');
            $table->integer('penggunaan_pengurangan_btg')->default(0)->after('penggunaan_pengurangan_volume');
            $table->integer('persediaan_akhir_btg')->default(0)->after('persediaan_akhir_volume');
        });

        // Tambah kolom jumlah_btg di laporan_mutasi_kayu_olahan
        Schema::table('laporan_mutasi_kayu_olahan', function (Blueprint $table) {
            $table->integer('persediaan_awal_btg')->default(0)->after('jenis_olahan');
            $table->integer('penambahan_btg')->default(0)->after('penambahan_volume');
            $table->integer('penggunaan_pengurangan_btg')->default(0)->after('penggunaan_pengurangan_volume');
            $table->integer('persediaan_akhir_btg')->default(0)->after('persediaan_akhir_volume');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('laporan_mutasi_kayu_bulat', function (Blueprint $table) {
            $table->dropColumn([
                'persediaan_awal_btg',
                'penambahan_btg',
                'penggunaan_pengurangan_btg',
                'persediaan_akhir_btg',
            ]);
        });

        Schema::table('laporan_mutasi_kayu_olahan', function (Blueprint $table) {
            $table->dropColumn([
                'persediaan_awal_btg',
                'penambahan_btg',
                'penggunaan_pengurangan_btg',
                'persediaan_akhir_btg',
            ]);
        });
    }
};
