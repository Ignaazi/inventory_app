<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('history_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('request_no')->unique();
            $table->string('sparepart_name');
            $table->string('sap_code');
            $table->integer('qty_req');
            $table->string('line_machine');
            $table->string('requestor');
            $table->string('approved_by'); // Nama Staff / Leader / SPV yang memproses
            $table->longText('signature_image')->nullable(); // Menyimpan Base64 tanda tangan ringkas
            $table->enum('status', ['APPROVED', 'REJECTED']); // Status akhir
            $table->timestamp('processed_at')->useCurrent(); // Waktu eksekusi approval
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('history_approvals');
    }
};