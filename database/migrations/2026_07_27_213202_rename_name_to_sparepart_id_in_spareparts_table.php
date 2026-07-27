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
        Schema::table('spareparts', function (Blueprint $table) {
            // Mengubah nama kolom 'name' menjadi 'sparepart_id' tanpa merusak data
            $table->renameColumn('name', 'sparepart_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spareparts', function (Blueprint $table) {
            // Mengembalikan ke 'name' jika migration di-rollback
            $table->renameColumn('sparepart_id', 'name');
        });
    }
};