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
            // 1. HAPUS KOLOM LAMA
            $table->dropColumn('creator_nik');

            // 2. SUNTIK KOLOM BARU YANG TERHUBUNG KE MASTER USERS
            // Dibuat nullable agar data lama yang tidak punya relasi id tidak menyebabkan error constraint
            $table->foreignId('users_id')
                  ->nullable()
                  ->after('stock_eng_id')
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
            // KEMBALIKAN KE STRUKTUR SEMULA JIKA DI-ROLLBACK
            $table->dropForeign(['users_id']);
            $table->dropColumn('users_id');
            
            $table->string('creator_nik')->nullable()->after('stock_eng_id');
        });
    }
};