<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Menggunakan DB Statement agar aman di semua versi Laravel & MySQL
        DB::statement("ALTER TABLE purchase_requests MODIFY COLUMN status ENUM('pending', 'checked', 'approved', 'rejected') NOT NULL DEFAULT 'pending'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE purchase_requests MODIFY COLUMN status ENUM('pending', 'checked', 'approved') NOT NULL DEFAULT 'pending'");
    }
};