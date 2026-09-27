<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@serpus.test'],
            [
                'name' => 'Administrator',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => UserRole::Admin,
            ],
        );

        User::firstOrCreate(
            ['email' => 'peminjam@serpus.test'],
            [
                'name' => 'Budi Peminjam',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => UserRole::Peminjam,
            ],
        );
    }
}
