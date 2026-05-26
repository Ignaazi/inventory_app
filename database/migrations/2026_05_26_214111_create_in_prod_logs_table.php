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
        // 💡 JALUR AMAN: Jika tabel sudah ada di MySQL kamu, kita sesuaikan urutan dan kolomnya
        if (Schema::hasTable('inProd_logs')) {
            
            Schema::table('inProd_logs', function (Blueprint $table) {
                
                // Menambahkan kolom no_nozzle jika belum ada
                if (!Schema::hasColumn('inProd_logs', 'no_nozzle')) {
                    $table->string('no_nozzle')->nullable()->after('line_id');
                }
                
                // Menambahkan kolom request_no jika belum ada
                if (!Schema::hasColumn('inProd_logs', 'request_no')) {
                    $table->string('request_no')->nullable()->after('transaction_out_id');
                }

                // Memastikan transaction_out_id tipenya string (VARCHAR)
                if (Schema::hasColumn('inProd_logs', 'transaction_out_id')) {
                    $table->string('transaction_out_id')->change();
                }
            });

        } else {
            
            // 💡 JALUR BARU: Jika tabel belum ada, buat struktur persis sesuai urutan request kamu
            Schema::create('inProd_logs', function (Blueprint $table) {
                // 1. Primary Key Utama (Otomatis mencakup No/ID)
                $table->id('inproduction_id');
                
                // 2. Data Akun User Login
                $table->string('nik'); 
                
                // 3. Integrasi Relasi (Urutan sesuai request)
                $table->unsignedBigInteger('line_id');         // FK ke list_line_productions (line_id)
                $table->string('no_nozzle');                  // Diambil dari stock_out_logs (no_nozzle)
                $table->string('transaction_out_id');         // FK ke stock_out_logs (VARCHAR)
                $table->string('request_no')->nullable();     // Penghubung 3 arah (stock_out_logs -> request_no)
                $table->unsignedBigInteger('barcode_id');      // FK ke db_barcodes (barcode_id)
                $table->unsignedBigInteger('stock_prod_id');   // FK ke stock_prods (id) untuk trigger tambah qty
                
                // 4. Data Qty In & Status
                $table->integer('qty_in');                     // Jumlah item yang masuk, memicu tambah qty di stock_prods
                $table->string('status')->default('success');  
                $table->string('remark')->nullable();          
                $table->text('comment')->nullable();           
                
                // 5. Timestamps (Otomatis mencakup created_at & updated_at / Date)
                $table->timestamps();                          
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inProd_logs');
    }
};