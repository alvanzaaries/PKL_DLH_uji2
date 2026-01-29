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
        // Add nama_custom to tptkb_sumber pivot table
        // (industri_jenis_produksi already has this column from previous migration)
        Schema::table('tptkb_sumber', function (Blueprint $table) {
            $table->string('nama_custom')->nullable()->after('kapasitas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tptkb_sumber', function (Blueprint $table) {
            $table->dropColumn('nama_custom');
        });
    }
};
