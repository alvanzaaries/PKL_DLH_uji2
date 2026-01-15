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
            $table->string('bulan')->nullable();
            $table->string('no_urut')->nullable();
            $table->string('jenis_sdh');

            $table->string('lhp_no')->nullable();
            $table->string('lhp_tanggal')->nullable();

            $table->decimal('volume', 20, 2)->nullable();
            $table->string('satuan')->nullable();
            $table->decimal('lhp_nilai', 20, 2)->nullable();

            $table->string('billing_no')->nullable();
            $table->string('billing_tanggal')->nullable();
            $table->decimal('billing_nilai', 20, 2)->nullable();

            $table->string('setor_tanggal')->nullable();
            $table->string('setor_bank')->nullable();
            $table->string('setor_ntpn')->nullable();
            $table->string('setor_ntb')->nullable();
            $table->decimal('setor_nilai', 20, 2)->nullable();

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
