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
        Schema::create('tptkb_sumber', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tptkb_id')->constrained('tpt_kb')->onDelete('cascade');
            $table->foreignId('master_sumber_id')->constrained('master_sumber')->onDelete('cascade');
            $table->decimal('kapasitas', 15, 2); // Kapasitas per sumber bahan baku
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tptkb_sumber');
    }
};
