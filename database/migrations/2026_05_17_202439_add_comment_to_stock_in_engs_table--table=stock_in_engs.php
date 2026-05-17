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
        // FIX: Diubah menjadi 'stock_in_logs' sesuai isi $table di Model
        Schema::table('stock_in_logs', function (Blueprint $table) {
            $table->text('comment')->nullable()->after('remark');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_in_logs', function (Blueprint $table) {
            $table->dropColumn('comment');
        });
    }
};