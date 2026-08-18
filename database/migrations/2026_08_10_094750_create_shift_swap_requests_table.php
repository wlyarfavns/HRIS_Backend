<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_swap_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();

            $table->foreignId('from_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('from_shift_assignment_id')->constrained('shift_assignments')->cascadeOnDelete();

            $table->foreignId('to_employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('to_shift_assignment_id')->constrained('shift_assignments')->cascadeOnDelete();

            $table->text('reason')->nullable();
            $table->boolean('peer_approved')->default(false); 
            $table->enum('status', ['pending_peer', 'pending_spv', 'approved', 'rejected'])->default('pending_spv');

            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_swap_requests');
    }
};