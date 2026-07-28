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
        Schema::table('production_requests', function (Blueprint $table) {
            // Mengubah tipe data kolom signature dari longText menjadi text tanpa hapus tabel
            $table->text('production_signature')->nullable()->change();
            $table->text('staff_signature')->nullable()->change();
            $table->text('spv_signature')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('production_requests', function (Blueprint $table) {
            // Kembalikan ke longText jika rollback
            $table->longText('production_signature')->nullable()->change();
            $table->longText('staff_signature')->nullable()->change();
            $table->longText('spv_signature')->nullable()->change();
        });
    }
};