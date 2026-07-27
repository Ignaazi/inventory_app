<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Modifikasi Tabel Users
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom last login
            $table->dropColumn(['last_login_at', 'last_login_ip']);
            
            // Rename kolom nim menjadi nik
            $table->renameColumn('nim', 'nik');
            
            // Tambah kolom untuk tanda tangan (path file foto ttd)
            $table->text('signature_path')->nullable()->after('profile_photo_path');
        });

        // 2. Modifikasi Tabel Password Reset Tokens (Karena merujuk ke nim)
        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->renameColumn('nim', 'nik');
        });
    }

    public function down(): void
    {
        // Kembalikan ke struktur semula jika di-rollback
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('last_login_at')->nullable()->after('is_active');
            $table->string('last_login_ip')->nullable()->after('last_login_at');
            $table->renameColumn('nik', 'nim');
            $table->dropColumn('signature_path');
        });

        Schema::table('password_reset_tokens', function (Blueprint $table) {
            $table->renameColumn('nik', 'nim');
        });
    }
};