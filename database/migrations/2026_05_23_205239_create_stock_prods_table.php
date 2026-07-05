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
        Schema::create('stock_prods', function (Blueprint $table) {
            // 1. ID Utama
            $table->id(); 
            
            // 2. Relasi ke tabel list_line_productions (Menghubungkan entitas line_id)
            $table->string('line_id', 100); 
            
            // 3. Relasi ke tabel spareparts (Di-set NULLABLE agar bisa ADD LINE dulu baru isi nozzle)
            $table->string('no_nozzle')->nullable(); 
            
            // 4. Relasi ke tabel stock_engs (Di-set NULLABLE untuk menampung alokasi dari Engineering nanti)
            $table->string('part_no')->nullable();
            $table->string('sap_code')->nullable();
            $table->string('category')->nullable(); 
            
            // 5. Data Transaksional Stok Produksi
            $table->integer('qty')->default(0);
            $table->integer('min_stock')->default(0);
            
            // 6. Created_at & Updated_at
            $table->timestamps();

            // PENTING: Opsional INDEX agar query pencarian relasi string jauh lebih cepat dan enteng
            $table->index('line_id');
            $table->index('no_nozzle');
            $table->index('part_no');
            $table->index('sap_code');
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