<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('list_line_productions', function (Blueprint $table) {
            $table->id();
            
            // Hapus ->after('id') karena posisi di sini otomatis berada di bawah 'id'
            $table->string('user_id')->nullable(); 
            
            // line_id unik untuk barcode/QR scanner protection
            $table->string('line_id')->unique(); 
            $table->string('no_line');
            $table->string('name_machine');
            $table->timestamps(); 

            // Hubungkan langsung ke kolom 'nim' di tabel 'users'
            $table->foreign('user_id')
                  ->references('nim') 
                  ->on('users')
                  ->onDelete('set null')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('list_line_productions', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        
        Schema::dropIfExists('list_line_productions');
    }
};