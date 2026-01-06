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
        Schema::create('reconciliation_facts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_detail_id')->constrained('reconciliation_details')->cascadeOnDelete();
            $table->foreignId('wilayah_id')->nullable();
            $table->foreignId('komoditas_id')->nullable();
            $table->decimal('volume', 15, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reconciliation_facts');
    }
};
