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
        Schema::table('stock_in_logs', function (Blueprint $table) {
            // Mengubah nama kolom 'comment' menjadi 'process_type' tanpa menghapus data
            $table->renameColumn('comment', 'process_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_in_logs', function (Blueprint $table) {
            // Mengembalikan nama kolom jika rollback
            $table->renameColumn('process_type', 'comment');
        });
    }
};