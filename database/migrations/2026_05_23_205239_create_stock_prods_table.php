<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_prods', function (Blueprint $table) {
            $table->id(); // Ini untuk "No" (Auto Increment)
            
            // Relasi ke tabel list_line_productions
            $table->unsignedBigInteger('line_id'); 
            
            // Kolom spesifik Nozzle
            $table->string('no_nozzle');
            
            // Relasi ke tabel productions_request (menggunakan nomor request string)
            $table->string('request_no')->nullable();
            
            // Data part & sap yang terintegrasi dengan stock_engs
            $table->string('part_no');
            $table->string('sap_code');
            
            // Relasi ke tabel db_barcodes
            $table->unsignedBigInteger('barcode_id')->nullable();
            
            // Relasi ke tabel stock_out_logs untuk trace barcode out
            $table->unsignedBigInteger('transaction_out_id')->nullable();
            
            // Kolom Quantity & Minimum Threshold Stock
            $table->integer('qty')->default(0);
            $table->integer('min_stock')->default(0);
            
            // Created_at & Updated_at
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_prods');
    }
};