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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('nim')->unique(); // 1. Ganti email menjadi nim dan tetap unique
            $table->string('role')->default('Production'); // 2. Tambah role untuk manajemen akses
            $table->string('password');
            $table->text('profile_photo_path')->nullable(); // 3. Tambahkan untuk foto profil jika pakai Jetstream
            $table->rememberToken();
            $table->timestamps();
        });

        // Tetap gunakan email untuk reset password (opsional) 
        // atau ganti primary key-nya jika ingin benar-benar tanpa email.
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary(); 
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};