<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The current PR flow uses the newer no_pr/sparepart_id columns. Keep the
     * old PR columns for compatibility, but do not require them on inserts.
     */
    public function up(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            foreach (['pr_code', 'name', 'nik', 'product', 'type_product'] as $column) {
                if (Schema::hasColumn('purchase_requests', $column)) {
                    $table->string($column)->nullable()->change();
                }
            }
        });
    }

    public function down(): void
    {
        // Keep the repair in place when rolling back newer migrations.
    }
};
