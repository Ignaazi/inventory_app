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
        Schema::table('purchase_requests', function (Blueprint $table) {
            // Menambahkan 3 kolom tanda tangan setelah kolom status
            $table->string('prepared_signature')->nullable()->after('status');
            $table->string('checked_signature')->nullable()->after('prepared_signature');
            $table->string('approved_signature')->nullable()->after('checked_signature');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_requests', function (Blueprint $table) {
            $table->dropColumn(['prepared_signature', 'checked_signature', 'approved_signature']);
        });
    }
};