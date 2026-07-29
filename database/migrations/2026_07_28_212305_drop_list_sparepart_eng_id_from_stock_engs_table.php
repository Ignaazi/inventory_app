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
        Schema::table('stock_engs', function (Blueprint $table) {
            // Menghapus kolom yang tidak berguna secara aman
            $table->dropColumn('list_sparepart_eng_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_engs', function (Blueprint $table) {
            // Mengembalikan kolom jika dilakukan rollback
            $table->bigInteger('list_sparepart_eng_id')->unsigned()->default(0)->after('id');
        });
    }
};