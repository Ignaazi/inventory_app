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
        Schema::create('history_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('request_no'); 
            $table->string('sparepart_name');
            $table->string('sap_code')->nullable();
            $table->integer('qty_req');
            $table->string('line_machine');
            $table->string('requestor');
            
            // 1. Bagian Staff Engineering
            $table->string('approved_by')->nullable(); // Nama Staff
            $table->longText('staff_signature')->nullable(); // TTD Staff (sebelumnya signature_image)
            
            // 2. Bagian SPV Engineering (Tambahan baru)
            $table->string('spv_name')->nullable();
            $table->longText('spv_signature')->nullable(); 
            
            // 3. Status
            $table->string('status'); 
            
            $table->timestamp('processed_at')->useCurrent(); 
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_approvals');
    }
};