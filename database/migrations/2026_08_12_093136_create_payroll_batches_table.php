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
        Schema::create('payroll_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained();
            $table->date('period_start');
            $table->date('period_end');
            
            // draft -> pending_finance -> approved_finance -> exported -> disbursed
            $table->string('status')->default('draft');
            
            $table->foreignId('submitted_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('submitted_at')->nullable();
            
            $table->foreignId('approved_finance_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_finance_at')->nullable();
            
            $table->timestamp('exported_at')->nullable();
            $table->timestamp('disbursed_at')->nullable();
            
            $table->timestamps();
            
            // Mencegah duplikasi batch untuk periode yang sama pada satu perusahaan
            $table->unique(['company_id', 'period_start', 'period_end']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payroll_batches');
    }
};