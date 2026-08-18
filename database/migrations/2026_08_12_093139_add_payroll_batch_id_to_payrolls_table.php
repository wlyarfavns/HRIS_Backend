<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->foreignId('payroll_batch_id')
                  ->nullable()
                  ->after('company_id')
                  ->constrained('payroll_batches')
                  ->nullOnDelete();
        });
    }


    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {

            $table->dropForeign(['payroll_batch_id']);
            $table->dropColumn('payroll_batch_id');
        });
    }
};