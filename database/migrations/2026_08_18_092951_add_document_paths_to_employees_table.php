<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('npwp_file_path')->nullable()->after('ktp_file_path');
            $table->string('bpjs_file_path')->nullable()->after('npwp_file_path');
        });
    }


    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['npwp_file_path', 'bpjs_file_path']);
        });
    }
};
