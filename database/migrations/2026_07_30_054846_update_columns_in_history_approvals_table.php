<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Jika migrasi ini bertugas membuat/menambahkan kolom sparepart_id
        Schema::table('history_approvals', function (Blueprint $table) {
            if (!Schema::hasColumn('history_approvals', 'sparepart_id')) {
                // Sesuaikan tipe data & posisi kolom jika migrasi ini memang membuat kolom baru
                $table->unsignedBigInteger('sparepart_id')->nullable()->after('id');
            }
        });

        // 2. AMANKAN QUERY UPDATE DATA DENGAN HASCOLUMN
        if (Schema::hasColumn('history_approvals', 'sparepart_id')) {
            DB::table('history_approvals')
                ->where('sparepart_id', 0)
                ->update(['sparepart_id' => null]); // Sesuaikan dengan logika update kamu
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('history_approvals', function (Blueprint $table) {
            if (Schema::hasColumn('history_approvals', 'sparepart_id')) {
                $table->dropColumn('sparepart_id');
            }
        });
    }
};