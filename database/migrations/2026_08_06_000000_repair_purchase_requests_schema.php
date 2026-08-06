<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Repair the purchase_requests schema used by the current PR/MR flow.
     *
     * Older migrations created a legacy version of this table and the later
     * relation migration only attempted to add foreign keys. This migration
     * is intentionally conditional so it works for both existing databases
     * and a fresh migration run.
     */
    public function up(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('purchase_requests', 'no_pr')) {
                $table->string('no_pr')->nullable()->unique();
            }

            if (!Schema::hasColumn('purchase_requests', 'user_id')) {
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('purchase_requests', 'sparepart_id')) {
                $table->foreignId('sparepart_id')
                    ->nullable()
                    ->constrained('spareparts')
                    ->nullOnDelete();
            }

            if (!Schema::hasColumn('purchase_requests', 'qty_pr')) {
                $table->unsignedInteger('qty_pr')->default(1);
            }

            if (!Schema::hasColumn('purchase_requests', 'expected_arrival_date')) {
                $table->dateTime('expected_arrival_date')->nullable();
            }

            if (!Schema::hasColumn('purchase_requests', 'remark')) {
                $table->text('remark')->nullable();
            }
        });

        // Preserve values from the legacy column names where possible.
        if (Schema::hasColumn('purchase_requests', 'pr_code')) {
            Schema::getConnection()->statement(
                'UPDATE purchase_requests SET no_pr = pr_code WHERE no_pr IS NULL'
            );
        }

        if (Schema::hasColumn('purchase_requests', 'qty')) {
            Schema::getConnection()->statement(
                'UPDATE purchase_requests SET qty_pr = qty WHERE qty_pr = 1 AND qty <> 1'
            );
        }

        if (Schema::hasColumn('purchase_requests', 'notes')) {
            Schema::getConnection()->statement(
                'UPDATE purchase_requests SET remark = notes WHERE remark IS NULL'
            );
        }
    }

    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_requests', 'sparepart_id')) {
                $table->dropConstrainedForeignId('sparepart_id');
            }

            if (Schema::hasColumn('purchase_requests', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }

            $columns = array_filter([
                Schema::hasColumn('purchase_requests', 'no_pr') ? 'no_pr' : null,
                Schema::hasColumn('purchase_requests', 'qty_pr') ? 'qty_pr' : null,
                Schema::hasColumn('purchase_requests', 'expected_arrival_date') ? 'expected_arrival_date' : null,
                Schema::hasColumn('purchase_requests', 'remark') ? 'remark' : null,
            ]);

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};
