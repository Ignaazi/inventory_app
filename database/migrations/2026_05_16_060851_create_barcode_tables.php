<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabel Type Barcode (Halaman Struktur Komponen)
        Schema::create('type_barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('char_type'); // TEXT, NUMBER, CODE, dll.
            $table->integer('char_length'); // 1-999
            $table->string('char_value'); // Potongan nilainya
            $table->json('components_json'); // Menyimpan full snapshot struktur untuk fitur import template
            $table->timestamps();
        });

        // Tabel DB Barcode (Halaman Final Barcode Data)
        Schema::create('db_barcodes', function (Blueprint $table) {
            $table->id();
            $table->string('barcode_type'); // QR CODE, DATA MATRIX, dll.
            $table->string('barcode_size'); // Ukuran mm
            $table->text('final_content'); // Hasil gabungan composite
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('type_barcodes');
        Schema::dropIfExists('db_barcodes');
    }
};