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
        Schema::create('stock_in_logs', function (Blueprint $table) {
            $table->id();

            // Menghubungkan log ini ke ID barang di table stock_eng
            $table->foreignId('stock_eng_id')->constrained('stock_engs')->onDelete('cascade');

            // Menghubungkan log ke tabel eng_material_receivings (Aman karena antrean file sudah di akhir)
            $table->foreignId('eng_material_receiving_id')
                  ->nullable()
                  ->constrained('eng_material_receivings')
                  ->onDelete('set null');

            $table->string('nik'); 
            $table->integer('qty_added'); 
            $table->string('status')->default('Success'); 
            $table->text('remark')->nullable(); 
            $table->text('comment')->nullable(); // Menjaga kolom comment bawaan DB lu sebelumnya
            $table->timestamps(); 
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_in_logs');
    }
};