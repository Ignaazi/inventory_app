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
        Schema::create('stock_engs', function (Blueprint $table) {
            $table->id();
            
            // 1. Hubungkan ke tabel raks
            $table->foreignId('rak_id')->constrained('raks')->onDelete('cascade');

            // 2. Gunakan Blueprint ini untuk memaksa tipe data BIGINT UNSIGNED yang identik dengan ID utama
            $table->unsignedBigInteger('sparepart_id');
            
            // 3. Kolom stok fisik
            $table->integer('qty')->default(0);
            $table->integer('min_stock')->default(0);
            $table->timestamps(); 
        });

        // 4. Kita matikan sementara pengecekan relasi agar MySQL tidak rewel saat menyusun struktur
        Schema::disableForeignKeyConstraints();

        Schema::table('stock_engs', function (Blueprint $table) {
            // Coba pasang relasi secara paksa ke target tabel 'spareparts'
            $table->foreign('sparepart_id')
                  ->references('id')
                  ->on('spareparts')
                  ->onDelete('cascade');
        });

        Schema::enableForeignKeyConstraints();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('stock_engs');
        Schema::enableForeignKeyConstraints();
    }
};