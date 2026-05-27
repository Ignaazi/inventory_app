<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table) {
            $table->id();
            $table->string('pr_code')->unique(); // PR/ENG/REQSPAREPART/2026/05/27/0001
            $table->string('name');              // muhammad ignazi
            $table->string('nik');               // auto dari user login
            $table->string('product');           // Kategori sparepart
            $table->string('type_product');      // Nama sparepart
            
            // 🌟 Kolom qty aman nangkring di sini
            $table->integer('qty')->default(1);  
            
            $table->enum('priority', ['normal', 'urgent'])->default('normal');
            $table->string('request_by')->default('ENGINEERING DEPARTMENT');
            $table->dateTime('request_date');
            $table->string('destination')->default('Costing & Procurement Room');
            $table->text('notes')->nullable();
            
            $table->enum('status', [
                'draft', 
                'waiting', 
                'waiting approval', 
                'approved', 
                'rejected', 
                'done'
            ])->default('draft');
            
            $table->timestamps();    
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};