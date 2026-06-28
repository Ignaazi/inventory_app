<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up() // 🌟 SUDAH DIGANTI JADI 'up', BIAR REKREASI TABELNYA JALAN
    {
        Schema::create('stock_return_logs', function (Blueprint $table) {
            $table->id();
            $table->string('return_id')->unique(); // Format kustom: RETURNEY001, dst.
            $table->string('nik');
            $table->string('request_sparepart_id')->nullable();
            $table->unsignedBigInteger('barcode_id');
            $table->unsignedBigInteger('stock_eng_id');
            $table->string('no_nozzle')->nullable();
            $table->unsignedBigInteger('rak_id')->nullable();
            $table->integer('qty_return'); // Disesuaikan dari qty_out menjadi qty_return
            $table->enum('status', ['SUCCESS', 'PENDING'])->default('SUCCESS');
            $table->string('remark')->default('MANUAL RETURN');
            $table->text('comment')->nullable();
            $table->timestamps();

            // Foreign Key Constraints (Sama persis dengan stock_out_logs lu)
            $table->foreign('barcode_id')->references('id')->on('db_barcodes')->onDelete('cascade');
            $table->foreign('stock_eng_id')->references('id')->on('stock_engs')->onDelete('cascade');
            $table->foreign('rak_id')->references('id')->on('raks')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('stock_return_logs');
    }
};