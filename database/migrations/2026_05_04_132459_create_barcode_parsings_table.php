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
    Schema::create('barcode_parsings', function (Blueprint $table) {
        $table->id();
        $table->string('sap_code')->unique(); // Kode unik untuk scanner
        $table->string('item_name');
        $table->string('description')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('barcode_parsings');
    }
};
