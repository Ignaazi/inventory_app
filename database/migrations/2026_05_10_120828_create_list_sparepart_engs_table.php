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
    Schema::create('list_sparepart_engs', function (Blueprint $table) {
        $table->id();
        $table->string('name'); // Nama Sparepart (misal: NOZZLE 711)
        $table->string('image')->nullable();
        $table->string('category');
        $table->integer('qty')->default(0);
        $table->timestamps(); // Ini untuk Update At
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('list_sparepart_engs');
    }
};
