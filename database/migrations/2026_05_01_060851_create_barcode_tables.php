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
        // Tabel Type Barcode (Struktur Komponen Pembuat Barcode)
        Schema::create('type_barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('char_type'); 
            $table->integer('char_length'); 
            $table->string('char_value'); 
            $table->json('components_json'); 
            $table->timestamps();
        });

        // Tabel DB Barcode (Halaman Final Barcode Data - Sekali Pakai)
        Schema::create('db_barcodes', function (Blueprint $table) {
            $table->id(); // Primary key internal
            
            // Kode Unik Barcode Custom (Contoh: SIIXENG001, SIIXENG002, dst.)
            $table->string('barcode_id')->unique();
            
            $table->string('barcode_type')->default('QR CODE');
            $table->string('barcode_size')->default('40x40 mm');
            $table->text('final_content'); // Hasil teks/content di dalam barcode
            
            // Kolom NIK Pembuat/Pencetak awal barcode
            $table->string('creator_nik')->nullable();
            
            // Status Siklus Barcode Sekali Pakai
            $table->enum('current_lifecycle', ['AVAILABLE', 'USED_IN', 'USED_OUT', 'RETURNED', 'DISPOSAL'])->default('AVAILABLE');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Matikan check foreign key biar pas drop table db_barcodes gak dicegat oleh barcode_parsings
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('type_barcodes');
        Schema::dropIfExists('db_barcodes');

        // Nyalakan kembali setelah proses drop selesai
        Schema::enableForeignKeyConstraints();
    }
};