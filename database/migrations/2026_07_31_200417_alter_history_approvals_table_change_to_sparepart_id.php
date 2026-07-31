<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('history_approvals', function (Blueprint $table) {
            // Mengubah nama kolom sparepart_name menjadi sparepart_id agar konsisten dengan entitas asli
            if (Schema::hasColumn('history_approvals', 'sparepart_name') && !Schema::hasColumn('history_approvals', 'sparepart_id')) {
                $table->renameColumn('sparepart_name', 'sparepart_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('history_approvals', function (Blueprint $table) {
            if (Schema::hasColumn('history_approvals', 'sparepart_id') && !Schema::hasColumn('history_approvals', 'sparepart_name')) {
                $table->renameColumn('sparepart_id', 'sparepart_name');
            }
        });
    }
};