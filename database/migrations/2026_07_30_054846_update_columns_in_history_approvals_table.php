<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // AMAN: Kita bersihkan angka 0 bermasalah tadi menjadi NULL dulu
        DB::table('history_approvals')->where('sparepart_id', 0)->update(['sparepart_id' => null]);

        Schema::table('history_approvals', function (Blueprint $table) {
            try {
                $table->dropForeign('history_approvals_sparepart_id_foreign');
            } catch (\Exception $e) {}

            // Ubah agar kolom ini boleh kosong (nullable) dulu demi menampung data lama
            $table->bigInteger('sparepart_id')->unsigned()->nullable()->change();
            
            // Hubungkan foreign key ke id tabel spareparts
            $table->foreign('sparepart_id')->references('id')->on('spareparts')->onDelete('cascade');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        Schema::table('history_approvals', function (Blueprint $table) {
            try { $table->dropForeign(['sparepart_id']); } catch (\Exception $e) {}
        });
    }
};