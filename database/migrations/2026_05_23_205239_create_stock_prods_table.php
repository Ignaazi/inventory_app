<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_prods', function (Blueprint $table) {
            $table->id(); // Ini untuk "No" (Auto Increment)
            
            // AMAN: Diubah jadi string karena menampung kode unik Alfanumerik (e.g., 'SIIXSMTLINE001')
            $table->string('line_id', 100); 
            
            // Kolom spesifik Nozzle
            $table->string('no_nozzle')->default('-');
            
            // Relasi ke tabel productions_request (menggunakan nomor request string)
            $table->string('request_no')->nullable()->default('-');
            
            // Data part & sap yang terintegrasi dengan stock_engs
            $table->string('part_no')->default('-');
            $table->string('sap_code')->default('-');
            
            // AMAN: Diubah jadi string karena di controller diisi format kode teks 'BC-XXXXX'
            $table->string('barcode_id')->nullable()->default('-');
            
            // AMAN: Diubah jadi string karena menampung token transaksi 'TXO-ENG-XXXXX'
            $table->string('transaction_out_id')->nullable()->default('-');
            
            // Kolom Quantity & Minimum Threshold Stock
            $table->integer('qty')->default(0);
            $table->integer('min_stock')->default(0);
            
            // Created_at & Updated_at
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_prods');
    }
};