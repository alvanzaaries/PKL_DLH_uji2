<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('reconciliation_files', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reconciliation_id')->constrained()->onDelete('cascade');
            $table->string('original_filename');
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size')->default(0);
            $table->binary('content');
            $table->timestamps();
        });

        // Laravel's schema builder doesn't expose a `longBlob()` column type.
        // Keep cross-DB compatibility, but upgrade to LONGBLOB when running on MySQL.
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE reconciliation_files MODIFY content LONGBLOB');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('reconciliation_files');
    }
};
