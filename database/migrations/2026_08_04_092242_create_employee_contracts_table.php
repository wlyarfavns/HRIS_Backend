<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employee_contracts', function (Blueprint $table) {

            $table->id();
            $table->foreignId('employee_id')->constrained()->cascadeOnDelete();
            $table->string('contract_number')->nullable()->unique();
            $table->enum('contract_type', ['PKWT', 'PKWTT']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->decimal('basic_salary',15,2)->default(0);
            $table->string('document_file')->nullable();
            $table->string('status')->default('Active'); 
            $table->text('notes')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employee_contracts');
    }
};