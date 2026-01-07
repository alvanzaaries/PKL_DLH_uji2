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
        Schema::create('industri_primer', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->text('alamat');
            $table->string('penanggungjawab');
            $table->string('kabupaten');
            $table->string('kontak');
            $table->string('pemberi_izin');
            $table->string('jenis_produksi');
            $table->string('kapasitas_izin');
            $table->string('nomor_izin');
            $table->enum('pelaporan', ['Aktif', 'Tidak Aktif', 'Pending'])->default('Pending');
            $table->string('dokumen_izin')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industri_primer');
    }
};
