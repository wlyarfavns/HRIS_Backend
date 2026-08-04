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
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // akun login karyawan (untuk Flutter)
            $table->foreignId('department_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('position_id')->nullable()->constrained()->nullOnDelete();

            $table->string('nip')->comment('Nomor Induk Pegawai, unik per company');
            $table->string('full_name');
            $table->string('email')->nullable();
            $table->string('phone', 20)->nullable();
            $table->text('address')->nullable();

            $table->date('join_date')->nullable();
            $table->enum('contract_type', ['pkwt', 'pkwtt'])->default('pkwt');
            $table->date('contract_end_date')->nullable();

            $table->decimal('basic_salary', 15, 2)->default(0);
            $table->enum('status', ['active', 'inactive', 'resigned'])->default('active');

            $table->timestamps();

            // NIP unik per company (bukan unik global, karena tiap company punya penomoran sendiri)
            $table->unique(['company_id', 'nip']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
