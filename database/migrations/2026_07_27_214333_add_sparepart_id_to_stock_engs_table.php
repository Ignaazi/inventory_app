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
        Schema::table('stock_engs', function (Blueprint $table) {
            // 1. Tambahkan kolom relasi ke tabel master sparepart
            // Menggunakan constrained('list_sparepart_engs') sesuai konvensi nama tabel Laravel untuk model ListSparepartEng
            $table->foreignId('list_sparepart_eng_id') 
                  ->after('id') // ditaruh setelah primary key id tabel stock
                  ->constrained('list_sparepart_engs')
                  ->onDelete('cascade'); // Jika master sparepart dihapus, stok otomatis terhapus
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_engs', function (Blueprint $table) {
            // Drop foreign key dan kolom jika migration di-rollback
            $table->dropForeign(['list_sparepart_eng_id']);
            $table->dropColumn('list_sparepart_eng_id');
        });
    }
};