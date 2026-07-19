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
            
            // 🌟 TAMBAHAN RELASI ID: Kunci utama agar alokasi otomatis follow Engineering
            // Di-set nullable karena saat ADD LINE (Lini Kosong), ID ini belum ada.
            $table->unsignedBigInteger('stock_eng_id')->nullable();
            
            // 3. Relasi ke tabel spareparts (Di-set NULLABLE agar bisa ADD LINE dulu baru isi nozzle)
            $table->string('no_nozzle')->nullable(); 
            
            // 4. Data Spek Cadangan (Di-set NULLABLE untuk menampung alokasi dari Engineering nanti)
            $table->string('part_no')->nullable();
            $table->string('sap_code')->nullable();
            $table->string('category')->nullable(); 
            
            // 5. Data Transaksional Stok Produksi
            $table->integer('qty')->default(0);
            $table->integer('min_stock')->default(0);
            
            // 6. Created_at & Updated_at
            $table->timestamps();

            // PENTING: Indexing untuk kecepatan pencarian data relasi
            $table->index('line_id');
            $table->index('stock_eng_id'); // Index tambahan untuk ID relasi baru
            $table->index('no_nozzle');
            $table->index('part_no');
            $table->index('sap_code');

            // OPSIONAL: Menerapkan Foreign Key Constraint agar data konsisten dengan master Engineering
            $table->foreign('stock_eng_id')
                  ->references('id')
                  ->on('stock_engs')
                  ->onDelete('set null'); // Jika data di stock_engs dihapus, di sini jadi null, tidak merusak aplikasi
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