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
        Schema::table('material_received', function (Blueprint $table) {
            // Menambahkan kolom qty_status bertipe ENUM tepat setelah kolom qty_received
            $table->enum('qty_status', ['open', 'closed'])->default('open')->after('qty_received');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('material_received', function (Blueprint $table) {
            // Menghapus kolom jika migration di-rollback
            $table->dropColumn('qty_status');
        });
    }
};