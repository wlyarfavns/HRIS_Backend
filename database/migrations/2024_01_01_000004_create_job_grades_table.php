<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('job_grades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('name');                 // misal: Staff, Supervisor, Manager
            $table->unsignedInteger('level')->default(1); // urutan level, makin besar makin senior
            $table->decimal('default_allowance', 15, 2)->nullable(); // default tunjangan jabatan
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('job_grades');
    }
};
