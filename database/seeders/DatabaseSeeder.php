<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\EngineeringOverview;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // --- 1. DATA USERS ---
        // Menggunakan updateOrCreate agar tidak error Duplicate Entry NIM
        $users = [
            [
                'nim'      => '123456',
                'name'     => 'Administrator',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ],
            [
                'nim'      => '654321',
                'name'     => 'Engineering User',
                'password' => Hash::make('password123'),
                'role'     => 'engineering',
            ],
            [
                'nim'      => '112233',
                'name'     => 'Costing User',
                'password' => Hash::make('password123'),
                'role'     => 'costing',
            ],
            [
                'nim'      => '445566',
                'name'     => 'Production User',
                'password' => Hash::make('password123'),
                'role'     => 'production',
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(['nim' => $userData['nim']], $userData);
        }

        // --- 2. DATA ENGINEERING OVERVIEW ---
        // Menggunakan updateOrCreate agar tidak error Duplicate Entry SAP-CODE
        $items = [
            [
                'sap_code'            => 'SAP-NOZ-001',
                'part_name'           => 'Nozzle Fuji NXT 0.4',
                'nozzle_type'         => '0.4mm',
                'current_stock'       => 2,
                'min_stock_threshold' => 10,
                'rack_position'       => 'A1-01',
                'status'              => 'Critical',
            ],
            [
                'sap_code'            => 'SAP-NOZ-002',
                'part_name'           => 'Nozzle Fuji NXT 0.7',
                'nozzle_type'         => '0.7mm',
                'current_stock'       => 8,
                'min_stock_threshold' => 5,
                'rack_position'       => 'A1-02',
                'status'              => 'Healthy',
            ],
        ];

        foreach ($items as $itemData) {
            EngineeringOverview::updateOrCreate(['sap_code' => $itemData['sap_code']], $itemData);
        }
    }
}