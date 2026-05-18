<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
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
            $table->id(); // Ini auto increment angka bawaan (1, 2, 3 untuk primary key internal)
            
            // 🌟 REVISI BARU: Kolom Kode Unik Barcode Custom Lu (SIIXENG001, SIIXENG002, dst.)
            $table->string('barcode_id')->unique(); 
            
            $table->string('barcode_type')->default('QR CODE'); 
            $table->string('barcode_size')->default('40x40 mm'); 
            $table->text('final_content'); // Hasil teks/content di dalam barcodenya
            
            // Status Siklus Barcode Sekali Pakai
            $table->enum('current_lifecycle', ['AVAILABLE', 'USED_IN', 'USED_OUT', 'RETURNED', 'DISPOSAL'])->default('AVAILABLE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_barcodes');
        Schema::dropIfExists('db_barcodes');
    }
};