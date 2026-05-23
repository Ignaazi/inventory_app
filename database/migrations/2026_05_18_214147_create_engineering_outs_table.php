<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_out_logs', function (Blueprint $table) {
            $table->id(); 
            $table->string('transaction_out_id')->unique(); 
            $table->string('nik'); 
            
            // RELASI DATABASE
            $table->unsignedBigInteger('request_sparepart_id')->nullable(); 
            $table->unsignedBigInteger('barcode_id'); 
            $table->unsignedBigInteger('stock_eng_id'); 
            
            // 🌟 TAMBAHAN: Kolom no_nozzle untuk histori nozzle
            $table->string('no_nozzle')->nullable();
            
            // 🌟 TAMBAHAN: Daftarkan kolom rak_id di sini
            $table->unsignedBigInteger('rak_id')->nullable(); 
            
            $table->integer('qty_out'); 
            $table->enum('status', ['SUCCESS', 'PENDING'])->default('SUCCESS');
            
            // 🌟 UPDATE REMARK: Diubah jadi string biasa agar fleksibel menampung custom remark
            $table->string('remark')->default('MANUAL OUT');
            
            $table->text('comment')->nullable(); 
            $table->timestamps(); 

            // FOREIGN KEY CONSTRAINTS
            $table->foreign('stock_eng_id')->references('id')->on('stock_engs')->onDelete('cascade');
            $table->foreign('barcode_id')->references('id')->on('db_barcodes')->onDelete('cascade');
            
            // 🌟 TAMBAHAN: Hubungkan foreign key ke tabel raks
            $table->foreign('rak_id')->references('id')->on('raks')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_out_logs');
    }
};