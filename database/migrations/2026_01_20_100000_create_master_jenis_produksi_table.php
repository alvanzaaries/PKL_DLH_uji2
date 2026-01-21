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
        Schema::create('master_jenis_produksi', function (Blueprint $table) {
            $table->id();
            $table->string('nama'); // Gergajian, Veneer, Plywood, Moulding, dll
            $table->enum('kategori', ['primer', 'sekunder', 'both'])->default('both'); // Untuk filter
            $table->string('satuan')->default('m³/tahun'); // Satuan kapasitas
            $table->text('keterangan')->nullable(); // Deskripsi tambahan
            $table->boolean('aktif')->default(true); // Status aktif/non-aktif
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('master_jenis_produksi');
    }
};
