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
        Schema::create('mapping_aliases', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // wilayah / komoditas
            $table->string('alias');
            $table->unsignedBigInteger('master_id');
            $table->timestamps();

            $table->index(['type', 'alias']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mapping_aliases');
    }
};
