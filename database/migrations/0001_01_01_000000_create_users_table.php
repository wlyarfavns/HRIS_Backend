<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            // Tambahkan relasi ke tabel companies
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');

            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        // (Biarkan Schema password_reset_tokens dan sessions tetap ada di bawahnya)
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};