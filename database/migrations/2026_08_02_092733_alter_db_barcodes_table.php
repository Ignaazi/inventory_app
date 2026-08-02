<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('db_barcodes', function (Blueprint $table) {
            // 1. AMANKAN USERS_ID LAMA (Jika terlanjur masuk di posisi lama pas migration 09:17)
            if (Schema::hasColumn('db_barcodes', 'users_id')) {
                $table->dropForeign(['users_id']);
                $table->dropColumn('users_id');
            }

            // 2. HAPUS CREATOR_NIK HANYA JIKA DIA MASIH ADA
            if (Schema::hasColumn('db_barcodes', 'creator_nik')) {
                $table->dropColumn('creator_nik');
            }

            // 3. SUNTIK USERS_ID BARU TEPAT DI SAMPING BARCODE_ID
            $table->foreignId('users_id')
                  ->nullable()
                  ->after('barcode_id') // 🔑 Posisinya pindah ke samping barcode_id, bro!
                  ->constrained('users')
                  ->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('db_barcodes', function (Blueprint $table) {
            $table->dropForeign(['users_id']);
            $table->dropColumn('users_id');
            
            // Kembalikan ke struktur awal migrasi sebelumnya jika di-rollback
            $table->string('creator_nik')->nullable()->after('stock_eng_id');
        });
    }
};