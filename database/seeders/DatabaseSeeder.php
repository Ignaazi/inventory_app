<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\EngineeringOverview;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // --- 1. DATA USERS (LOGIN MENGGUNAKAN NIK) ---
        // Pencarian updateOrCreate menggunakan kolom 'nik' agar tidak error duplicate entry
        $users = [
            [
                'nik'       => '123456',
                'name'      => 'Administrator',
                'email'     => 'muhammad.anwar@student.president.ac.id',
                'role'      => 'admin',
                'password'  => Hash::make('password123'),
                'is_active' => 1,
            ],
            [
                'nik'       => '654321',
                'name'      => 'Engineering User',
                'email'     => 'aji@student.president.ac.id',
                'role'      => 'engineering',
                'password'  => Hash::make('password123'),
                'is_active' => 1,
            ],
            [
                'nik'       => '112233',
                'name'      => 'Costing User',
                'email'     => 'christin@student.president.ac.id',
                'role'      => 'costing',
                'password'  => Hash::make('password123'),
                'is_active' => 1,
            ],
            [
                'nik'       => '445566',
                'name'      => 'Production User',
                'email'     => 'reza@student.president.ac.id',
                'role'      => 'production',
                'password'  => Hash::make('password123'),
                'is_active' => 1,
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['nik' => $userData['nik']], 
                $userData
            );
        }

        // --- 2. DATA ENGINEERING OVERVIEW ---
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
            EngineeringOverview::updateOrCreate(
                ['sap_code' => $itemData['sap_code']], 
                $itemData
            );
        }
    }
}