<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('engineering_overviews', function (Blueprint $table) {
        $table->id();
        $table->string('sap_code')->unique(); // SAP-CODE Nozzle/Part
        $table->string('part_name');
        $table->string('nozzle_type')->nullable(); // Khusus tipe nozzle
        $table->integer('current_stock')->default(0);
        $table->integer('min_stock_threshold')->default(5);
        $table->string('rack_position')->nullable();
        $table->enum('status', ['Healthy', 'Warning', 'Critical'])->default('Healthy');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineering_overviews');
    }
};
