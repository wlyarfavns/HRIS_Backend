<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->string('nik', 16)->nullable()->after('full_name');
            $table->string('npwp', 20)->nullable()->after('phone');
            $table->string('bpjs_number', 20)->nullable()->after('npwp');
            $table->decimal('basic_salary', 15, 2)->nullable()->after('employment_status');
            $table->string('ktp_file_path')->nullable()->after('basic_salary');


            $table->string('gender')->nullable()->change();
            $table->date('birth_date')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['nik', 'npwp', 'bpjs_number', 'basic_salary', 'ktp_file_path']);
        });
    }
};