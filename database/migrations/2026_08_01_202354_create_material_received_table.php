<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('material_received', function (Blueprint $table) {
            $table->id();
            // Format Custom Code: MR00000001 sampai MR99999999
            $table->string('no_mr')->unique(); 
            
            // Relasi ke Purchase Request & User
            $table->foreignId('purchase_request_id')->constrained('purchase_requests')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Kolom Qty & Catatan Tambahan
            $table->integer('qty_received')->default(0); // Qty aktual yang diterima saat form dibuat
            $table->text('remark')->nullable();
            
            // Status & Flow Approval (Costing -> Eng Staff -> Admin)
            $table->enum('status', ['pending', 'checked', 'approved'])->default('pending');
            
            // Sistem Tanda Tangan Digital (Signature)
            $table->string('prepared_signature')->nullable(); // Oleh Costing
            $table->string('checked_signature')->nullable();  // Oleh Staff Engineering
            $table->string('approved_signature')->nullable(); // Oleh Admin
            
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('material_received');
    }
};