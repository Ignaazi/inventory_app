<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barcode_out_daily_counters', function (Blueprint $table) {
            $table->id();
            $table->string('date_key', 6)->unique();
            $table->unsignedInteger('last_counter')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcode_out_daily_counters');
    }
};
