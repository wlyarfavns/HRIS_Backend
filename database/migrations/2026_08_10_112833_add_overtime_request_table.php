<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {

    public function up(): void
    {
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();


            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();


            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('hours', 5, 2);
            $table->string('project')->nullable();
            $table->text('notes')->nullable();


            $table->decimal('salary_snapshot', 15, 2); 
            $table->decimal('overtime_pay', 15, 2)->nullable(); 


            $table->enum('status', ['pending_spv', 'approved_spv', 'locked', 'rejected'])
                  ->default('pending_spv');


            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();


            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('payroll_id')->nullable()->constrained()->nullOnDelete(); 


            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('rejection_reason')->nullable();

            $table->timestamps();
        });
    }


    public function down(): void
    {

        Schema::dropIfExists('overtime_requests');
    }
};