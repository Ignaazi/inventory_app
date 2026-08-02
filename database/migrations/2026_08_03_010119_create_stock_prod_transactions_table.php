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
        Schema::create('stock_prod_transactions', function (Blueprint $table) {
            $table->id();
            
            // Kode unik transaksi produksi (Contoh: TX-PROD-IN-1785679076)
            $table->string('tx_id')->unique(); 
            
            // Operator Produksi (Foreign Key ke tabel users) - Selaras dengan users_id di Eng
            $table->foreignId('users_id')
                  ->constrained('users')
                  ->onDelete('cascade'); 
            
            // Lokasi Stok Produksi yang terdampak (Line & Sparepart)
            $table->foreignId('stock_prods_id')
                  ->constrained('stock_prods')
                  ->onDelete('cascade'); 
            
            /**
             * JEMBATAN LIFECYCLE UTAMA KE GUDANG ENGINEERING
             * Tipenya unsignedBigInteger agar klop dengan bigint(20) milik stock_eng_transactions
             */
            $table->unsignedBigInteger('stock_eng_tx_id')->nullable();

            /**
             * KOLOM KESELARASAN MASTER DATA[cite: 8]
             * Ditambahkan agar sistem bisa tracking id barcode fisik & nomor dokumen request[cite: 8]
             */
            $table->unsignedBigInteger('db_barcodes_id')->nullable();
            $table->unsignedBigInteger('production_request_id')->nullable();
            
            // Tipe Transaksi Lini Produksi (IN, OUT, atau RETURN)
            $table->enum('tx_type', ['in', 'out', 'return']); 
            
            // Sub-Kategori untuk alur OUT (Broken / Rusak untuk Disposal VS Lost / Barang Hilang)
            $table->enum('out_category', ['broken', 'lost'])->nullable(); 
            
            // NIK Karyawan yang menghilangkan barang (Wajib diisi jika out_category = 'lost')
            $table->string('nik_karyawan')->nullable(); 
            
            // Jumlah qty (Disamakan int(11) sesuai stock_eng_transactions)[cite: 8]
            $table->integer('qty_transaction');
            
            // Metode Input data (Scan Barcode atau Input Manual)[cite: 8]
            $table->enum('process_type', ['scan', 'manual']);
            
            // Upload foto bukti fisik (khusus barang rusak/broken sebelum diserahkan ke disposal)[cite: 8]
            $table->string('photo_path')->nullable();
            
            // Status Transaksi[cite: 8]
            // - 'pending': Menunggu orang gudang scan QR di modul disposal/return
            // - 'success': Transaksi sah/selesai
            $table->enum('status', ['success', 'pending', 'failed'])->default('success');
            
            // Note / Kronologi (Disamakan text default null)[cite: 8]
            $table->text('remark')->nullable();
            
            $table->timestamps();

            /**
             * DEFINISI HUBUNGAN RELASI (FOREIGN KEYS) & INDEXING
             */
            // Relasi ke tabel Transaksi Engineering[cite: 8]
            $table->foreign('stock_eng_tx_id')
                  ->references('id')
                  ->on('stock_eng_transactions')
                  ->onDelete('set null');

            // Relasi ke tabel Master Barcode[cite: 8]
            $table->foreign('db_barcodes_id')
                  ->references('id')
                  ->on('db_barcodes')
                  ->onDelete('set null');

            // Relasi ke tabel Dokumen Request Produksi[cite: 8]
            $table->foreign('production_request_id')
                  ->references('id')
                  ->on('production_requests')
                  ->onDelete('set null');

            // Optimasi performa database untuk query filter report & monitoring dashboard
            $table->index('tx_type');
            $table->index('out_category');
            $table->index('status');
            $table->index('process_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_prod_transactions');
    }
};