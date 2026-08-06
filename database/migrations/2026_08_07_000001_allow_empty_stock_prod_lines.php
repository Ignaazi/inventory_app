<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('stock_prods', 'sparepart_id')) {
            Schema::table('stock_prods', function (Blueprint $table) {
                $table->unsignedBigInteger('sparepart_id')->nullable()->change();
            });
        }
    }

    public function down(): void
    {
        // Existing empty-line placeholders must be completed before rollback.
    }
};
