<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Jalankan migrasi.
     */
    public function up(): void
    {
        // 1. Kondisi jika tabel BELUM ada sama sekali (buat baru dari awal)
        if (!Schema::hasTable('eng_material_receivings')) {
            Schema::create('eng_material_receivings', function (Blueprint $table) {
                $table->id();
                $table->string('receiving_code')->unique(); 
                $table->string('pr_code');                 
                $table->string('item_name')->nullable();
                $table->string('supplier_name')->nullable();
                $table->integer('qty_received');
                $table->string('lot_no')->nullable();
                $table->string('created_by_nik')->nullable(); 
                $table->string('created_by_name');
                $table->string('status')->default('draft'); 
                $table->text('engineering_notes')->nullable();
                $table->text('costing_notes')->nullable();
                $table->string('costing_signature_path')->nullable();
                $table->string('costing_stamp_path')->nullable();
                $table->string('eng_signature_path')->nullable();
                $table->string('eng_spv_signature_path')->nullable();
                $table->timestamps();
            });
        }

        // 2. Kondisi jika tabel SUDAH ada, kita suntikkan kolom yang kurang agar Controller tidak error
        Schema::table('eng_material_receivings', function (Blueprint $table) {
            if (!Schema::hasColumn('eng_material_receivings', 'signature_status')) {
                $table->string('signature_status')->default('submitted_by_costing')->after('status');
            }
            if (!Schema::hasColumn('eng_material_receivings', 'engineering_signature_path')) {
                $table->string('engineering_signature_path')->nullable()->after('eng_signature_path');
            }
            if (!Schema::hasColumn('eng_material_receivings', 'engineering_spv_signature_path')) {
                $table->string('engineering_spv_signature_path')->nullable()->after('eng_spv_signature_path');
            }
        });
    }

    /**
     * Batalkan migrasi.
     */
    public function down(): void
    {
        Schema::dropIfExists('eng_material_receivings');
    }
};