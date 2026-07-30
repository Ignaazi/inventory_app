<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Matikan check foreign key biar proses drop & create mulus tanpa ganjalan
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Hapus total tabel lama yang strukturnya berantakan
        Schema::dropIfExists('history_approvals');

        // Bikin struktur baru yang fresh dan langsung konek pake foreignId
        Schema::create('history_approvals', function (Blueprint $table) {
            $table->id();
            
            // Relasi FK presisi terikat ke production_requests.id
            $table->foreignId('production_request_id')
                  ->nullable()
                  ->constrained('production_requests')
                  ->onDelete('set null'); // Kalau data transaksi asli diapus, log history tetep aman
            
            // Kolom snapshot untuk pencarian text & backup log
            $table->string('request_no');
            $table->string('nik')->nullable();
            $table->string('approver_name')->nullable();
            $table->string('sparepart_name')->nullable(); 
            $table->integer('qty_req');
            $table->string('line_machine')->nullable(); 
            $table->string('status');
            $table->text('remark')->nullable();
            
            // Waktu pemrosesan log
            $table->timestamp('processed_at')->useCurrent();
            $table->timestamps();
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        Schema::dropIfExists('history_approvals');
    }
};