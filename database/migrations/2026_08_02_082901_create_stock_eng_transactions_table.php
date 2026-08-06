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
        // 🔑 PENCEGAHAN ERROR: Hapus tabel lama jika terlanjur nyangkut di MySQL
        Schema::dropIfExists('stock_eng_transactions');

        Schema::create('stock_eng_transactions', function (Blueprint $table) {
            // 1. id
            $table->id();
            
            // 2. tx_id (Nomor Resi Unik, cth: TXENG001)
            $table->string('tx_id')->unique();
            
            // 3. users_id (Relasi ke table users)
            $table->foreignId('users_id')->constrained('users')->onDelete('cascade');
            
            // 4. stock_engs_id (Relasi utama ke saldo & rak)
            $table->foreignId('stock_engs_id')->constrained('stock_engs')->onDelete('cascade');
            
            // 5. db_barcodes_id (Nullable karena transaksi IN awal bisa jadi belum ada barcode)
            $table->foreignId('db_barcodes_id')->nullable()->constrained('db_barcodes')->onDelete('set null');
            
            // 6. tx_type
            $table->enum('tx_type', ['in', 'out', 'return', 'disposal']);
            
            // 7. qty_transaction
            $table->integer('qty_transaction');
            
            // 8. process_type
            $table->enum('process_type', ['scan', 'manual']);
            
            // 9. photo_path (Bukti foto khusus disposal)
            $table->string('photo_path')->nullable();
            
            // 10. status
            $table->enum('status', ['success', 'pending', 'failed'])->default('success');
            
            // 11. remark
            $table->text('remark')->nullable();
            
            // 12. created_at & updated_at
            $table->timestamps();

            // Optimasi performa pencarian query riwayat transaksi
            $table->index('tx_type');
            $table->index('process_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_eng_transactions');
    }
};