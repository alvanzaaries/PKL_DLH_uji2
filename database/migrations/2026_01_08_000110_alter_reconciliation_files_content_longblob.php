<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasTable('reconciliation_files')) {
            return;
        }

        $driver = DB::getDriverName();

        // On MySQL/MariaDB, Blueprint::binary() maps to BLOB (64KB max).
        // Uploaded Excel files can exceed that size, so upgrade to LONGBLOB.
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE reconciliation_files MODIFY content LONGBLOB');
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('reconciliation_files')) {
            return;
        }

        $driver = DB::getDriverName();

        // Best-effort revert for MySQL/MariaDB.
        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement('ALTER TABLE reconciliation_files MODIFY content BLOB');
        }
    }
};
