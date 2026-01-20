<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin account (from HEAD)
        User::updateOrCreate(
            ['email' => 'admin@sipjateng.test'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );

        // Regular users (from HEAD)
        $users = [
            ['name' => 'User 1', 'email' => 'user1@sipjateng.test'],
            ['name' => 'User 2', 'email' => 'user2@sipjateng.test'],
            ['name' => 'User 3', 'email' => 'user3@sipjateng.test'],
        ];

        foreach ($users as $u) {
            User::updateOrCreate(
                ['email' => $u['email']],
                [
                    'name' => $u['name'],
                    'password' => Hash::make('password'),
                    'role' => 'user',
                ]
            );
        }

        // Call UserSeeder from Incoming if it exists (for additional data)
        if (class_exists(\Database\Seeders\UserSeeder::class)) {
            $this->call(\Database\Seeders\UserSeeder::class);
        }
        // Call KphSeeder from Incoming if it exists (for additional data)
        if (class_exists(\Database\Seeders\KphSeeder::class)) {
            $this->call(\Database\Seeders\KphSeeder::class);
        }
    }
}
