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
        Schema::create('laporan_mutasi_kayu_bulat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan')->onDelete('cascade');
            
            $table->string('jenis_kayu');
            $table->float('persediaan_awal_volume');
            $table->float('penggunaan_pengurangan_volume');
            $table->float('persediaan_akhir_volume');
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_mutasi_kayu_bulat');
    }
};
