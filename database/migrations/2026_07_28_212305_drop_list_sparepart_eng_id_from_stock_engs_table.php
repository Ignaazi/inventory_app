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
            // Cek terlebih dahulu apakah kolom benar-benar ada sebelum di-drop
            if (Schema::hasColumn('stock_engs', 'list_sparepart_eng_id')) {
                $table->dropColumn('list_sparepart_eng_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_engs', function (Blueprint $table) {
            // Cek dulu agar saat rollback tidak membuat kolom ganda jika sudah ada
            if (!Schema::hasColumn('stock_engs', 'list_sparepart_eng_id')) {
                $table->bigInteger('list_sparepart_eng_id')->unsigned()->default(0)->after('id');
            }
        });
    }
};