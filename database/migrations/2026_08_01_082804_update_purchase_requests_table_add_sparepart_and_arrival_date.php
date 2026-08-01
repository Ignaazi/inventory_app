<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Ubah status enum secara aman tanpa lewat engine compiler Laravel
        try {
            // Ubah ke VARCHAR dulu sementara agar data 'done' lama tidak crash
            DB::statement("ALTER TABLE purchase_requests MODIFY COLUMN status VARCHAR(255) NOT NULL DEFAULT 'pending'");
            
            // Set semua data lama ke 'pending' sesuai keinginan struktur baru
            DB::statement("UPDATE purchase_requests SET status = 'pending'");
            
            // Kunci kembali menjadi ENUM baru yang lu minta
            DB::statement("ALTER TABLE purchase_requests MODIFY COLUMN status ENUM('pending', 'checked', 'approved') NOT NULL DEFAULT 'pending'");
        } catch (\Exception $e) {
            // Jika sudah terlanjur berubah, lewati safely
        }

        // 2. Pasang Foreign Key resmi jika belum terikat
        try {
            DB::statement("ALTER TABLE purchase_requests ADD CONSTRAINT purchase_requests_user_id_foreign FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE CASCADE");
        } catch (\Exception $e) {}

        try {
            DB::statement("ALTER TABLE purchase_requests ADD CONSTRAINT purchase_requests_sparepart_id_foreign FOREIGN KEY (sparepart_id) REFERENCES spareparts (id) ON DELETE CASCADE");
        } catch (\Exception $e) {}
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Safely blank
    }
};