<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('overtime_requests', function (Blueprint $table) {
            $table->id();
            
            // Relasi Utama
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            
            // Detail Waktu
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->decimal('hours', 5, 2);
            $table->string('project')->nullable();
            $table->text('notes')->nullable();
            
            // Kalkulasi Finansial
            $table->decimal('salary_snapshot', 15, 2); // basic_salary saat pengajuan
            $table->decimal('overtime_pay', 15, 2)->nullable(); // hasil kalkulasi Depnaker

            // Status Pengajuan
            $table->enum('status', ['pending_spv', 'approved_spv', 'locked', 'rejected'])
                  ->default('pending_spv');

            // Tracking Approval
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            // Tracking HR/Finance Lock & Payroll Integration
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('locked_at')->nullable();
            $table->foreignId('payroll_id')->nullable()->constrained()->nullOnDelete(); // link ke tabel payrolls

            // Tracking Penolakan
            $table->foreignId('rejected_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('rejection_reason')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Wajib ditambahkan agar bisa di-rollback (php artisan migrate:rollback)
        Schema::dropIfExists('overtime_requests');
    }
};