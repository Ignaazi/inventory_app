<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_requests', function (Blueprint $table) {
            // Nama staff engineering yang menyetujui (sudah ada di file lo)
            $table->string('approved_by')->nullable()->after('status');
            
            // Ganti/tambahkan ini untuk menampung TTD fisik Engineering (Path file)
            $table->string('signature_path')->nullable()->after('approved_by');
    
            // --- TAMBAHAN BARU UNTUK OTORISASI PRODUKSI (BASE64 CANVAS) ---
            $table->longText('production_signature')->nullable()->after('requestor');
            $table->longText('production_stamp')->nullable()->after('production_signature');
        });
    }
    
    public function down(): void
    {
       
    }
};