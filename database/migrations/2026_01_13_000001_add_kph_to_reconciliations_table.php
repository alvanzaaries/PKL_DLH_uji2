<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->string('kph')->nullable()->after('quarter')->index();
        });
    }

    public function down(): void
    {
        Schema::table('reconciliations', function (Blueprint $table) {
            $table->dropIndex(['kph']);
            $table->dropColumn('kph');
        });
    }
};
