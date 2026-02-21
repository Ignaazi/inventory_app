<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Menambahkan kolom role untuk membedakan akses module
            // enum membantu membatasi input agar tidak terjadi typo data
            $table->enum('role', ['admin', 'engineering', 'production', 'costing'])
                  ->after('email')
                  ->default('production');
            
            // Opsional: Tambahkan status akun jika diperlukan
            $table->boolean('is_active')->default(true)->after('role');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['role', 'is_active']);
        });
    }
};