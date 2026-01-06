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
        Schema::create('reconciliation_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_id')->constrained()->onDelete('cascade');
            $table->string('wilayah');
            $table->string('jenis_sdh');
            $table->json('raw_data')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_details');
    }
};
