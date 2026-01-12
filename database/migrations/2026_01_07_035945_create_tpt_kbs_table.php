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
        Schema::create('tpt_kb', function (Blueprint $table) {
            $table->id();
            $table->foreignId('industri_id')->constrained('industries')->onDelete('cascade');
            $table->string('pemberi_izin');
            $table->string('sumber_bahan_baku');
            $table->string('kapasitas_izin');
            $table->date('masa_berlaku');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tpt_kb');
    }
};
