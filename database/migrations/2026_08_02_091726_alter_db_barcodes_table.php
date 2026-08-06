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
            // Hapus creator_nik jika masih ada di DB
            if (Schema::hasColumn('db_barcodes', 'creator_nik')) {
                $table->dropColumn('creator_nik');
            }

            // Buat kolom users_id baru jika belum ada
            if (!Schema::hasColumn('db_barcodes', 'users_id')) {
                $table->foreignId('users_id')
                      ->nullable()
                      ->after('stock_eng_id')
                      ->constrained('users')
                      ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('db_barcodes', function (Blueprint $table) {
            // Hapus users_id tanpa dropForeign manual agar tidak memicu error 1091
            if (Schema::hasColumn('db_barcodes', 'users_id')) {
                $table->dropConstrainedForeignId('users_id');
            }
            
            // Kembalikan creator_nik jika belum ada
            if (!Schema::hasColumn('db_barcodes', 'creator_nik')) {
                $table->string('creator_nik')->nullable()->after('stock_eng_id');
            }
        });
    }
};