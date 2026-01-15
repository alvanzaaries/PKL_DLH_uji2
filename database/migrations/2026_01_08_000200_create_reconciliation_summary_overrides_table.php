<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reconciliation_summary_overrides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_id')->constrained()->onDelete('cascade');
            // metric: total_volume | total_nilai_lhp
            $table->string('metric');
            // for total_volume metric: identifies unit (Ton, M3, Lbr, etc)
            $table->string('satuan')->nullable();
            $table->double('value')->default(0);
            $table->timestamps();

            $table->unique(['reconciliation_id', 'metric', 'satuan'], 'unique_override_key');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliation_summary_overrides');
    }
};
