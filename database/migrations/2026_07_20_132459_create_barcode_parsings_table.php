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
        Schema::create('barcode_parsings', function (Blueprint $table) {
            $table->id();
            
            // Relasi Utama
            $table->unsignedBigInteger('barcode_db_id');      
            $table->unsignedBigInteger('production_request_id'); 
            
            // NIK Operator
            $table->string('nik'); 
            
            // Data Transaksi
            $table->integer('qty_parsed')->default(1);
            $table->text('description')->nullable();
            $table->timestamps();

            // Setup Foreign Keys
            $table->foreign('barcode_db_id')->references('id')->on('db_barcodes')->onDelete('cascade');
            $table->foreign('production_request_id')->references('id')->on('production_requests')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('barcode_parsings');
        Schema::enableForeignKeyConstraints();
    }
};