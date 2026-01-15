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
        Schema::create('laporan_penerimaan_kayu_bulat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('laporan_id')->constrained('laporan')->onDelete('cascade');
            
            $table->string('nomor_dokumen');
            $table->date('tanggal');
            $table->string('asal_kayu');
            $table->string('jenis_kayu');
            $table->integer('jumlah_batang');
            $table->float('volume');
            $table->text('keterangan')->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('laporan_penerimaan_kayu_bulat');
    }
};
