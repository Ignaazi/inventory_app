<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * IN rows do not have an OUT barcode and OUT rows do not have an IN
     * barcode. A production request may also be processed in multiple batches.
     */
    public function up(): void
    {
        Schema::table('barcode_parsings', function (Blueprint $table) {
            if (!Schema::hasColumn('barcode_parsings', 'material_received_id')) {
                $table->foreignId('material_received_id')
                    ->nullable()
                    ->after('production_request_id')
                    ->constrained('material_received')
                    ->onDelete('cascade');
            }

        });

        // Keep the existing foreign keys while changing only nullability.
        DB::statement(
            'ALTER TABLE barcode_parsings MODIFY COLUMN production_request_id BIGINT UNSIGNED NULL'
        );
        DB::statement(
            'ALTER TABLE barcode_parsings MODIFY COLUMN barcode_in_id BIGINT UNSIGNED NULL'
        );

        $headerIndexes = collect(Schema::getIndexes('barcode_parsing_headers'));
        $productionRequestUnique = $headerIndexes->firstWhere(
            'name',
            'barcode_parsing_headers_production_request_id_unique'
        );

        if ($productionRequestUnique) {
            // MySQL uses the unique index to back the foreign key. Replace it
            // with a normal index before removing uniqueness.
            DB::statement(
                'ALTER TABLE barcode_parsing_headers ADD INDEX barcode_parsing_headers_production_request_id_index (production_request_id)'
            );
            DB::statement(
                'ALTER TABLE barcode_parsing_headers DROP INDEX barcode_parsing_headers_production_request_id_unique'
            );
        }
    }

    public function down(): void
    {
        // Keep partial parsing support when rolling back newer migrations.
    }
};
