<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // NAMA TABEL RESMI DIUBAH MENJADI stock_out_logs
        Schema::create('stock_out_logs', function (Blueprint $table) {
            $table->id(); 
            $table->string('transaction_out_id')->unique(); // ENGOUT001, ENGOUT002, dst.
            $table->string('nik'); // Menangkap NIK/NIM Akun yang Login
            
            // Relasi-relasi Database
            $table->unsignedBigInteger('request_sparepart_id')->nullable(); 
            $table->unsignedBigInteger('barcode_id')->nullable(); 
            $table->unsignedBigInteger('stock_eng_id'); // Terhubung ke DB Master Stock Eng
            
            $table->integer('qty_out'); 
            $table->enum('status', ['SUCCESS', 'PENDING'])->default('SUCCESS');
            $table->enum('remark', ['SCAN OUT', 'MANUAL OUT']);
            $table->text('comment')->nullable(); 
            $table->timestamps(); // Menghasilkan created_at (DATE)

            // Foreign Key Constraint ke tabel master stock_engs
            $table->foreign('stock_eng_id')->references('id')->on('stock_engs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_out_logs');
    }
};