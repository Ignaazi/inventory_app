<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_requests', function (Blueprint $blueprint) {
            // Menambahkan foreign key relasi ke master data
            $blueprint->foreignId('list_line_production_id')
                      ->nullable()
                      ->after('id')
                      ->constrained('list_line_productions')
                      ->onDelete('set null');

            $blueprint->foreignId('sparepart_id')
                      ->nullable()
                      ->after('list_line_production_id')
                      ->constrained('spareparts')
                      ->onDelete('set null');
                      
            // Optional: Mengubah kolom lama menjadi nullable jika ingin tetap mempertahankan history teks lama
            $blueprint->string('line_machine')->nullable()->change();
            $blueprint->string('sparepart_name')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('production_requests', function (Blueprint $blueprint) {
            $blueprint->dropForeign(['list_line_production_id']);
            $blueprint->dropForeign(['sparepart_id']);
            $blueprint->dropColumn(['list_line_production_id', 'sparepart_id']);
        });
    }
};