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
        // Drop table lama terlebih dahulu agar tidak terjadi crash struktural
        Schema::dropIfExists('stock_prods');

        Schema::create('stock_prods', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel list_line_productions (menggunakan id dari list_line_productions)[cite: 4]
            $table->foreignId('line_id')->constrained('list_line_productions')->onDelete('cascade');
            
            // Relasi ke tabel spareparts (persis seperti konsep stock_engs)[cite: 3]
            $table->foreignId('sparepart_id')->constrained('spareparts')->onDelete('cascade');
            
            // Kolom Inti Stock Tracking
            $table->integer('qty')->default(0);
            $table->integer('min_stock')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_prods');
    }
};