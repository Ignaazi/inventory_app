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
        Schema::create('spareparts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('category');
            $table->string('image')->nullable();
            
            // HAPUS INI: $table->integer('qty');
            // TAMBAHKAN INI (Gunakan decimal supaya aman kalau ada koma/milimeter detail):
            $table->decimal('length', 8, 2)->nullable();
            $table->decimal('width', 8, 2)->nullable();
            $table->decimal('thickness', 8, 2)->nullable();
            
            $table->timestamps();
        });
    }
};
