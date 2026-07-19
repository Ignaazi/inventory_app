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
        // 💡 PENGAMAN: Hapus tabel 'spareparts' terlebih dahulu jika ternyata masih nyangkut di DB
        Schema::dropIfExists('spareparts');

        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            
            // 🌟 Kolom baru tanpa 'after' (Otomatis berada di bawah id dan di atas name)
            $table->string('sap_code')->nullable();
            $table->string('part_number')->nullable();

            $table->string('name');
            $table->string('category');
            $table->string('image')->nullable();
            
            // Kolom dimensi detail nozzle / sparepart engineering
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('thickness', 8, 2)->nullable();
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spareparts');
    }
};