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
        Schema::create('barcode_parsing_headers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            $table->enum('mode', ['IN', 'OUT']);
            
            // Unique constraint agar 1 ID dokumen hanya bisa diproses 1x di tingkat DB
            $table->foreignId('material_received_id')
                  ->nullable()
                  ->unique()
                  ->constrained('material_received')
                  ->onDelete('cascade');
                  
            $table->foreignId('production_request_id')
                  ->nullable()
                  ->unique()
                  ->constrained('production_requests')
                  ->onDelete('cascade');

            $table->integer('total_qty')->default(1);
            $table->enum('status', ['completed', 'failed'])->default('completed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_parsing_headers');
    }
};