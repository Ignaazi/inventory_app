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
        Schema::create('coating_material_receivings', function (Blueprint $table) {
            $table->id();
            $table->string('receiving_id')->unique(); // Contoh: RC-CTG-20260708-001
            $table->string('material_code');
            $table->string('material_name');
            $table->string('lot_no');
            $table->integer('qty_received');
            $table->string('nik_receiver'); // Menyimpan NIK yang login (sebagai TTD Digital)
            $table->string('status')->default('RECEIVED');
            $table->text('comment')->nullable(); // Catatan tambahan kondisi barang
            $table->timestamps(); // Otomatis membuat kolom created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coating_material_receivings');
    }
};