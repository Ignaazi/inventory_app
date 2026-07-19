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
            // Menambahkan jembatan ke tabel stock_engs setelah kolom final_content
            // Di-set nullable() supaya aman jika ada jenis barcode non-engineering ke depannya
            $table->foreignId('stock_eng_id')
                  ->nullable()
                  ->after('final_content')
                  ->constrained('stock_engs')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('db_barcodes', function (Blueprint $table) {
            // Lepas foreign key dan hapus kolom jika di-rollback
            $table->dropForeign(['stock_eng_id']);
            $table->dropColumn('stock_eng_id');
        });
    }
};