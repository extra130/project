<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Create default users for all roles.
     * Passwords set to password123 for dev — change before production.
     */
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name'     => 'Admin',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );

        // Editor user
        User::firstOrCreate(
            ['email' => 'editor@example.com'],
            [
                'name'     => 'Editor',
                'password' => Hash::make('password123'),
                'role'     => 'editor',
            ]
        );

        // Viewer user
        User::firstOrCreate(
            ['email' => 'viewer@example.com'],
            [
                'name'     => 'Viewer',
                'password' => Hash::make('password123'),
                'role'     => 'viewer',
            ]
        );

        $this->command->info('Users seeded: admin@example.com / editor@example.com / viewer@example.com (all: password123)');
    }
}
