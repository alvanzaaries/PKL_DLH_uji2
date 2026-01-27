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
        Schema::table('industri_jenis_produksi', function (Blueprint $table) {
            // Change kapasitas_izin from string to unsigned integer
            $table->unsignedInteger('kapasitas_izin')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('industri_jenis_produksi', function (Blueprint $table) {
            // Rollback to string type
            $table->string('kapasitas_izin')->change();
        });
    }
};
