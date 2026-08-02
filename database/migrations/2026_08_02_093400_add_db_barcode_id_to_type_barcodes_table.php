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
        Schema::table('type_barcodes', function (Blueprint $table) {
            // SUNTIK RELASI KUNCI: Menghubungkan setiap pecahan komponen ke master barcodenya
            $table->foreignId('db_barcode_id')
                  ->nullable() // Dibuat nullable agar data lama aman saat proses migrasi
                  ->after('id') // Diletakkan rapi tepat di bawah kolom id
                  ->constrained('db_barcodes')
                  ->onDelete('cascade'); // Jika barcode utama dihapus, riwayat parser otomatis ikut terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('type_barcodes', function (Blueprint $table) {
            // Kembalikan struktur data seperti semula jika di-rollback
            $table->dropForeign(['db_barcode_id']);
            $table->dropColumn('db_barcode_id');
        });
    }
};