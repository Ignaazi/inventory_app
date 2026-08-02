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
        Schema::create('stock_eng_transactions', function (Blueprint $table) {
            $table->id();
            
            // 1. IDENTITAS UTAMA TRANSAKSI (NOMOR RESI GLOBAL)
            // Menampung counter global urut seperti: TXENG001, TXENG002, dst.
            $table->string('tx_number')->unique(); 
            
            // NIK / NIM operator atau staff engineering yang melakukan eksekusi
            $table->string('nik', 50); 
            
            // 2. RELASI MASTER SALDO & LOKASI GUDANG
            // Mengunci ke tabel master saldo stock_engs yang lo punya
            $table->foreignId('stock_eng_id')->constrained('stock_engs')->onDelete('cascade'); 
            
            // Mengunci ke lokasi Rak saat kejadian transaksi (nullable jika tidak di rak)
            $table->foreignId('rak_id')->nullable()->constrained('raks')->onDelete('set null'); 
            
            // 3. JEMBATAN PELACAK BARCODE & DOKUMEN REFERENSI
            // Menghubungkan ke ID stiker di tabel db_barcodes (Penting untuk validasi FIFO pas SCAN OUT/RETURN)
            $table->unsignedBigInteger('barcode_db_id')->nullable(); 
            
            // Menghubungkan ke ID Dokumen PR/Material Receiving (Hanya terisi jika tipenya MANUAL IN dari supplier)
            $table->unsignedBigInteger('eng_material_receiving_id')->nullable(); 
            
            // 4. PARAMETER UTAMA MUTASI BARANG (LOGIKA LEGER 🔑)
            // Menentukan arah gerak barang di gudang Engineering
            $table->enum('tx_type', ['IN', 'OUT', 'RETURN', 'DISPOSAL']); 
            
            // Menentukan metode eksekusi di lapangan
            $table->enum('process_type', ['Manual', 'Scan']); 
            
            // Jumlah fisik barang yang bermutasi (Selalu catat angka positif, misal: 1, 4, 10)
            $table->integer('qty'); 
            
            // 5. ATRIBUT PENDUKUNG & BUKTI FISIK LAPANGAN
            $table->string('no_nozzle')->nullable()->default('-'); 
            
            // Tempat menyimpan path foto (Wajib/opsional untuk bukti test photo saat OUT atau foto hancur saat DISPOSAL)
            $table->string('photo_path')->nullable(); 
            
            // Status akhir transaksi untuk menghindari data gantung
            $table->enum('status', ['SUCCESS', 'PENDING', 'FAILED'])->default('SUCCESS'); 
            
            // 6. CATATAN RIWAYAT
            $table->text('remark')->nullable();  // Otomatis diisi oleh sistem (cth: "AUTO GENERATE BARCODE IN")
            $table->text('comment')->nullable(); // Diisi manual jika staff mengetik keterangan tambahan
            
            $table->timestamps();

            // KUNCI INDEXING: Biar kalau data sudah puluhan ribu, query buat halaman History/Laporan tetap secepat kilat!
            $table->index('tx_type');
            $table->index('process_type');
            $table->index('barcode_db_id');
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