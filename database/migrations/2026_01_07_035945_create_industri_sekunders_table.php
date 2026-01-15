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
        Schema::create('industri_sekunder', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industri_id')->constrained('industries')->onDelete('cascade');
            $table->string('pemberi_izin');
            $table->string('jenis_produksi'); // jenis produksi/komoditas
            $table->string('kapasitas_izin');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industri_sekunder');
    }
};
