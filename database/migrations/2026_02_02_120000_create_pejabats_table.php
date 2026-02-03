<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pejabats', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('nip')->nullable();
            $table->string('pangkat')->nullable(); // e.g. Pembina Utama Muda
            $table->string('jabatan')->nullable(); // e.g. Kepala Dinas ...
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        // Insert default data
        DB::table('pejabats')->insert([
            'nama' => 'Widi Hartanto',
            'nip' => '-',
            'pangkat' => 'Pembina Utama Muda',
            'jabatan' => 'Kepala Dinas Lingkungan Hidup dan Kehutanan Provinsi Jawa Tengah',
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pejabats');
    }
};
