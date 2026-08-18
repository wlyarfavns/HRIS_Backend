<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('payroll_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->date('period_start');
            $table->date('period_end');


            $table->string('status')->default('draft');

            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();

            $table->foreignId('approved_finance_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_finance_at')->nullable();

            $table->timestamp('exported_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();

            $table->timestamps();


            $table->unique(['company_id', 'period_start', 'period_end']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('payroll_batches');
    }
};