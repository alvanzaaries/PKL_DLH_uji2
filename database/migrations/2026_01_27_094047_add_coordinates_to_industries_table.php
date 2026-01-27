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
        Schema::table('industries', function (Blueprint $table) {
            // Add latitude and longitude columns
            // Using decimal(10, 8) for latitude (range: -90 to 90)
            // Using decimal(11, 8) for longitude (range: -180 to 180)
            $table->decimal('latitude', 10, 8)->nullable()->after('kabupaten');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('industries', function (Blueprint $table) {
            $table->dropColumn(['latitude', 'longitude']);
        });
    }
};
