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
        Schema::table('stock_eng_transactions', function (Blueprint $table) {
            // SUNTIK RELASI DOKUMEN: Menghubungkan mutasi stok langsung ke tiket request produksi
            $table->foreignId('production_request_id')
                  ->nullable() // Wajib nullable karena transaksi tipe IN (Barang Masuk Gudang) tidak punya RequestProd
                  ->after('db_barcodes_id') // Diposisikan rapi di bawah foreign key barcode
                  ->constrained('production_requests')
                  ->onDelete('set null'); // Jika dokumen dihapus, catatan riwayat mutasi barang di jurnal tidak boleh hilang
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_eng_transactions', function (Blueprint $table) {
            $table->dropForeign(['production_request_id']);
            $table->dropColumn('production_request_id');
        });
    }
};