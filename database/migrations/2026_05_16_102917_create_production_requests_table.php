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
        Schema::create('production_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique(); 
            $table->string('sparepart_name');       
            $table->string('sap_code');             
            $table->integer('qty_req');
            $table->string('line_machine');         
            
            // 1. Otorisasi dari Sisi Production (Base64 Canvas)
            $table->string('requestor');            
            $table->longText('production_signature')->nullable();
            $table->longText('production_stamp')->nullable();
            
            // 2. Status Document (Sudah include 'Draft')
            $table->enum('status', ['Draft', 'Pending', 'Approved', 'Rejected'])->default('Draft');
            
            // 3. Otorisasi dari Sisi Engineering (PENTING BIAR GAK ERROR PAS APPROVAL!)
            $table->string('approved_by')->nullable();
            $table->string('signature_path')->nullable(); // Path TTD Fisik Eng
            
            // 4. Catatan jika ditolak
            $table->text('reject_remark')->nullable(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('production_requests');
    }
};