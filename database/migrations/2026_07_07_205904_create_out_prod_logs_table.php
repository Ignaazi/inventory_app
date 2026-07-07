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
        Schema::create('outProd_logs', function (Blueprint $table) {
            $table->id('outproduction_id'); 
            
            // Tambahkan kolom ini untuk relasi ke history IN Production
            $table->bigInteger('inproduction_id')->unsigned()->nullable(); 
            
            $table->string('nik');
            $table->bigInteger('line_id')->unsigned();
            $table->string('no_nozzle');
            $table->string('transaction_out_id')->unique();
            $table->string('request_no')->nullable();
            $table->bigInteger('barcode_id')->unsigned();
            $table->bigInteger('stock_prod_id')->unsigned();
            $table->integer('qty_out');
            $table->string('status')->default('success');
            $table->string('remark')->nullable();
            $table->text('comment')->nullable();
            
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('outProd_logs');
    }
};