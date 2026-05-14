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
    Schema::create('stock_in_logs', function (Blueprint $table) {
        $table->id();
        // Menghubungkan log ini ke ID barang di table stock_eng
        $table->foreignId('stock_eng_id')->constrained('stock_engs')->onDelete('cascade');
        $table->string('nik'); // Mengambil NIM/NIK user yang login
        $table->integer('qty_added'); // Jumlah stok yang baru masuk
        $table->string('status')->default('Success'); // Success, Pending, atau Not Complete
        $table->text('remark')->nullable(); // Keterangan tambahan
        $table->timestamps(); // Ini otomatis mencatat kolom DATE (created_at)
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_in_logs');
    }
};
