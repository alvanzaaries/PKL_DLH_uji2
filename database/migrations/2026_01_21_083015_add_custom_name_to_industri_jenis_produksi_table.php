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
        Schema::table('industri_jenis_produksi', function (Blueprint $table) {
            $table->string('nama_custom')->nullable()->after('kapasitas_izin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('industri_jenis_produksi', function (Blueprint $table) {
            $table->dropColumn('nama_custom');
        });
    }
};
