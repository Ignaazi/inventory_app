<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_out_logs', function (Blueprint $table) {
            $table->id(); // Primary Key internal transaksi
            $table->string('transaction_out_id')->unique(); // ENGOUT001, ENGOUT002
            $table->string('nik'); // NIK/NIM operator yang login
            
            // RELASI DATABASE
            $table->unsignedBigInteger('request_sparepart_id')->nullable(); 
            
            // 🌟 KUNCI: barcode_id ini terhubung ke id internal tabel db_barcodes
            $table->unsignedBigInteger('barcode_id'); 
            
            $table->unsignedBigInteger('stock_eng_id'); // Terhubung ke Master Stock Eng
            
            $table->integer('qty_out'); 
            $table->enum('status', ['SUCCESS', 'PENDING'])->default('SUCCESS');
            $table->enum('remark', ['SCAN OUT', 'MANUAL OUT']);
            $table->text('comment')->nullable(); 
            $table->timestamps(); // Menghasilkan created_at dan updated_at

            // FOREIGN KEY CONSTRAINTS
            $table->foreign('stock_eng_id')->references('id')->on('stock_engs')->onDelete('cascade');
            
            // 🌟 HUBUNGKAN KE TABEL db_barcodes
            $table->foreign('barcode_id')->references('id')->on('db_barcodes')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_out_logs');
    }
};