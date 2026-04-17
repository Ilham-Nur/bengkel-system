<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator',
                'email' => 'admin@bengkel.test',
                'password' => 'password',
                'role' => User::ROLE_ADMIN,
            ]
        );

        User::updateOrCreate(
            ['username' => 'pelanggan'],
            [
                'name' => 'Pelanggan',
                'email' => 'pelanggan@bengkel.test',
                'password' => 'password',
                'role' => User::ROLE_PELANGGAN,
            ]
        );
    }
}
