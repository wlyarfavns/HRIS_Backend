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
        Schema::table('payrolls', function (Blueprint $table) {
            $table->foreignId('payroll_batch_id')
                  ->nullable()
                  ->after('company_id')
                  ->constrained('payroll_batches')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Drop foreign key terlebih dahulu sebelum drop kolom
            $table->dropForeign(['payroll_batch_id']);
            $table->dropColumn('payroll_batch_id');
        });
    }
};