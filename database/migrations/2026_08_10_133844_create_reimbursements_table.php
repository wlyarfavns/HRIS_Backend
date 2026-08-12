<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reimbursements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();

            $table->string('category');           // Bensin & Parkir Client, dll
            $table->text('description')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('receipt_path')->nullable();

            $table->date('claim_date');

            // Alur: pending_spv -> pending_hr -> pending_finance -> approved / rejected
            $table->enum('status', [
                'pending_spv',
                'pending_hr',
                'pending_finance',
                'approved',
                'rejected',
            ])->default('pending_spv');

            $table->foreignId('spv_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('spv_approved_at')->nullable();

            $table->foreignId('hr_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('hr_reviewed_at')->nullable();

            $table->foreignId('finance_reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('finance_reviewed_at')->nullable();

            $table->text('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reimbursements');
    }
};