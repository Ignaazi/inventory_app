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
        Schema::table('barcode_parsings', function (Blueprint $table) {
            $table->foreignId('parsing_header_id')
                  ->nullable()
                  ->after('id')
                  ->constrained('barcode_parsing_headers')
                  ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('barcode_parsings', function (Blueprint $table) {
            $table->dropForeign(['parsing_header_id']);
            $table->dropColumn('parsing_header_id');
        });
    }
};