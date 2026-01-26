<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$tables = DB::select("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name");

echo "=== DAFTAR TABEL DI DATABASE ===\n\n";
foreach ($tables as $table) {
    echo "- " . $table->name . "\n";
}
