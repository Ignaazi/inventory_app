<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_received_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('pr_code');
            
            $table->integer('qty_received');
            $table->string('lot_no')->nullable();
            
            // 📝 TTD 1: Staff Costing
            $table->string('costing_staff_nik')->nullable();
            $table->string('costing_staff_name')->nullable();
            $table->timestamp('costing_signed_at')->nullable();
            $table->string('costing_signature_path')->nullable(); // 🌟 Kolom Gambar TTD Costing
            
            // 📝 TTD 2: Staff Engineering
            $table->string('engineering_staff_nik')->nullable();
            $table->string('engineering_staff_name')->nullable();
            $table->timestamp('engineering_signed_at')->nullable();
            $table->string('engineering_signature_path')->nullable(); // 🌟 Kolom Gambar TTD Staff Eng
            
            // 📝 TTD 3: Supervisor Engineering
            $table->string('engineering_spv_nik')->nullable();
            $table->string('engineering_spv_name')->nullable();
            $table->timestamp('engineering_spv_signed_at')->nullable();
            $table->string('engineering_spv_signature_path')->nullable(); // 🌟 Kolom Gambar TTD SPV Eng
            
            // Status Approval Berjenjang
            $table->enum('signature_status', [
                'incoming',           
                'pending_checking',   
                'pending_approval',   
                'completed',          
                'rejected'            
            ])->default('incoming'); 

            $table->text('costing_notes')->nullable();
            $table->text('engineering_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_received_signatures');
    }
};