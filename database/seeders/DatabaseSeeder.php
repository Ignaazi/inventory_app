<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin (Full Access)
        User::create([
            'name'     => 'Administrator',
            'nim'      => '123456', 
            'password' => Hash::make('password123'),
            'role'     => 'admin',
        ]);

        // 2. Akun Engineering (Manage Stock & Nozzles)
        User::create([
            'name'     => 'Engineering User',
            'nim'      => '654321',
            'password' => Hash::make('password123'),
            'role'     => 'engineering',
        ]);

        // 3. Akun Costing (Manage Purchase Requests)
        User::create([
            'name'     => 'Costing User',
            'nim'      => '112233',
            'password' => Hash::make('password123'),
            'role'     => 'costing',
        ]);

        // 4. Akun Production (Line Request & Scanner)
        User::create([
            'name'     => 'Production User',
            'nim'      => '445566',
            'password' => Hash::make('password123'),
            'role'     => 'production',
        ]);
    }
}