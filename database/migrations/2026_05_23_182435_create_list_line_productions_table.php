<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list_line_productions', function (Blueprint $table) {
            $table->id();
            // line_id unik untuk barcode/QR scanner protection agar tidak tertukar saat scan out
            $table->string('line_id')->unique(); 
            $table->string('no_line');
            $table->string('name_machine');
            $table->timestamps(); // Otomatis membuat created_at dan updated_at
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('list_line_productions');
    }
};