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
        Schema::table('barcode_parsings', function (Blueprint $table) {
            // 1. HAPUS RELASI DAN KOLOM LAMA YANG REDUNDAN
            // Hapus foreign key bawaan dari kolom barcode_db_id terlebih dahulu
            $table->dropForeign(['barcode_db_id']);
            
            // Hapus kolom-kolom lama yang sudah tidak terpakai
            $table->dropColumn(['barcode_db_id', 'nik', 'description']);

            // 2. SUNTIK KOLOM BARU YANG DIUPDATE
            // Menambahkan users_id tepat setelah kolom id untuk menggantikan NIK
            $table->foreignId('users_id')->after('id')->constrained('users')->onDelete('cascade'); 
            
            // Kolom 'production_request_id' TIDAK DIHAPUS karena sudah benar mengunci ke production_requests
            
            // Menambahkan dual-barcode tepat setelah production_request_id
            $table->foreignId('barcode_in_id')->after('production_request_id')->constrained('db_barcodes')->onDelete('cascade'); 
            $table->foreignId('barcode_out_id')->after('barcode_in_id')->nullable()->constrained('db_barcodes')->onDelete('set null'); 
            
            // Kolom 'qty_parsed' TETAP DIPERTAHANKAN dari tabel lama
            
            // Menambahkan status dan remark tepat setelah qty_parsed
            $table->enum('status', ['success', 'pending', 'failed'])->after('qty_parsed')->default('success');
            $table->text('remark')->after('status')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barcode_parsings', function (Blueprint $table) {
            // KEMBALIKAN KE STRUKTUR SEMULA JIKA DI-ROLLBACK
            $table->dropForeign(['users_id']);
            $table->dropForeign(['barcode_in_id']);
            $table->dropForeign(['barcode_out_id']);
            $table->dropColumn(['users_id', 'barcode_in_id', 'barcode_out_id', 'status', 'remark']);

            // Kembalikan kolom lama seperti semula
            $table->foreignId('barcode_db_id')->after('id')->constrained('db_barcodes')->onDelete('cascade');
            $table->string('nik')->after('production_request_id'); 
            $table->text('description')->after('qty_parsed')->nullable(); 
        });
    }
};