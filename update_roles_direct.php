<?php

// Direct SQL update for user roles
require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Updating User Roles ===\n\n";

try {
    // Get current users
    $users = DB::select('SELECT id, name, email, role FROM users');
    
    echo "Current users:\n";
    foreach ($users as $user) {
        echo "  ID: {$user->id}, Name: {$user->name}, Role: " . ($user->role ?? 'NULL') . "\n";
    }
    
    echo "\nUpdating NULL roles to 'admin'...\n";
    
    // Update NULL roles
    $affected = DB::update("UPDATE users SET role = 'admin' WHERE role IS NULL OR role = ''");
    
    echo "Updated {$affected} user(s)\n\n";
    
    // Verify
    $users = DB::select('SELECT id, name, email, role FROM users');
    echo "After update:\n";
    foreach ($users as $user) {
        echo "  ID: {$user->id}, Name: {$user->name}, Role: {$user->role}\n";
    }
    
    echo "\n✓ Done!\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
