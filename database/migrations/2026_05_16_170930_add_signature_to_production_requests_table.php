<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('production_requests', function (Blueprint $table) {
            $table->string('approved_by')->nullable()->after('status');
            $table->string('signature_path')->nullable()->after('approved_by');
        });
    }

    public function down(): void
    {
        Schema::table('production_requests', function (Blueprint $table) {
            $table->dropColumn(['approved_by', 'signature_path']);
        });
    }
};