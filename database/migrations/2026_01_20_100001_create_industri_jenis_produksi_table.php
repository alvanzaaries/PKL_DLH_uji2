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
        Schema::create('industri_jenis_produksi', function (Blueprint $table) {
            $table->id();
            $table->string('industri_type'); // Polymorphic type: App\Models\IndustriPrimer atau App\Models\IndustriSekunder
            $table->unsignedBigInteger('industri_id'); // ID dari industri_primer atau industri_sekunder
            $table->foreignId('jenis_produksi_id')->constrained('master_jenis_produksi')->onDelete('cascade');
            $table->string('kapasitas_izin'); // Kapasitas spesifik untuk jenis produksi ini
            $table->timestamps();

            // Index untuk polymorphic relationship
            $table->index(['industri_type', 'industri_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('industri_jenis_produksi');
    }
};
