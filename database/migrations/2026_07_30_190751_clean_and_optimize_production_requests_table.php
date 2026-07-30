<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        DB::table('production_requests')->delete();

        Schema::table('production_requests', function (Blueprint $table) {
            $table->dropColumn([
                'sparepart_name', 
                'sap_code', 
                'line_machine', 
                'requestor', 
                'production_stamp', 
                'staff_name', 
                'staff_signature', 
                'staff_stamp', 
                'spv_name', 
                'spv_stamp', 
                'approved_by', 
                'signature_path'
            ]);
        });

        Schema::table('production_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->string('status', 255)->default('Pending')->change();
            $table->text('engineering_signature')->nullable()->after('production_signature');
        });

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    public function down(): void
    {
        Schema::table('production_requests', function (Blueprint $table) {
            try {
                $table->dropForeign(['user_id']);
            } catch (\Exception $e) {}
            
            try {
                $table->dropColumn(['user_id', 'engineering_signature']);
            } catch (\Exception $e) {}
        });
    }
};