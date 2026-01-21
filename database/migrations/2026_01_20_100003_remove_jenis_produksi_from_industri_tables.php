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
        // Hapus kolom jenis_produksi dari industri_primer
        Schema::table('industri_primer', function (Blueprint $table) {
            $table->dropColumn('jenis_produksi');
        });

        // Hapus kolom jenis_produksi dari industri_sekunder
        Schema::table('industri_sekunder', function (Blueprint $table) {
            $table->dropColumn('jenis_produksi');
        });

        // Pindahkan kapasitas_izin juga (opsional, karena sekarang di pivot)
        // Tapi kita tetap biarkan di tabel utama sebagai fallback
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan kolom jenis_produksi
        Schema::table('industri_primer', function (Blueprint $table) {
            $table->string('jenis_produksi')->nullable()->after('pemberi_izin');
        });

        Schema::table('industri_sekunder', function (Blueprint $table) {
            $table->string('jenis_produksi')->nullable()->after('pemberi_izin');
        });
    }
};
