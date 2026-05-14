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
        $seedUsers = [
            [
                'name' => 'System Admin',
                'email' => 'admin@example.com',
                'password' => 'AdminPassphrase!2026',
                'role' => User::ROLE_ADMIN,
            ],
            [
                'name' => 'Club Employee',
                'email' => 'employee@example.com',
                'password' => 'EmployeePassphrase!2026',
                'role' => User::ROLE_EMPLOYEE,
            ],
            [
                'name' => 'Team Fan',
                'email' => 'fan@example.com',
                'password' => 'FanPassphrase!2026',
                'role' => User::ROLE_FAN,
            ],
        ];

        foreach ($seedUsers as $seedUser) {
            User::updateOrCreate(
                ['email' => $seedUser['email']],
                [
                    'name' => $seedUser['name'],
                    'password' => Hash::make($seedUser['password']),
                    'role' => $seedUser['role'],
                ]
            );
        }
    }
}
