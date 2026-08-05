<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->string('employee_id')->unique();

            $table->string('full_name');
            $table->string('email')->unique();
            $table->string('phone', 20)->nullable();
            $table->enum('gender', ['L', 'P']);
            $table->string('birth_place')->nullable();
            $table->date('birth_date')->nullable();
            $table->text('address')->nullable();

            $table->foreignId('department_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->foreignId('position_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('join_date');

            $table->enum('employment_status', [
                'PKWT',
                'PKWTT'
            ]);

            $table->enum('status', [
                'pending',
                'active',
                'inactive',
                'resigned'
            ])->default('pending');

            $table->string('activation_token')
                ->nullable()
                ->unique();

            $table->timestamp('activation_expired_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};