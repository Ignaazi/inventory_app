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
            
            // 1. Hubungkan ke tabel raks (Wajib ada tabel raks dulu sebelum ini jalan)
            // Kolom ini akan menggantikan id_rak yang lama
            $table->foreignId('rak_id')->constrained('raks')->onDelete('cascade');

            $table->string('no_nozzle')->comment('Nomor Nozzle');
            $table->string('part_no')->nullable();
            $table->string('sap_code')->nullable();
            $table->string('category')->nullable();
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
        // Matikan foreign key check dulu biar gak error pas drop tabel yang berelasi
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('stock_engs');
        Schema::enableForeignKeyConstraints();
    }
};