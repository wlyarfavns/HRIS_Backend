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
        // Mengubah kolom email menjadi nullable di tabel users
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });

        // Mengubah kolom email menjadi nullable di tabel employees
        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Mengembalikan kolom email menjadi wajib diisi (not null)
        Schema::table('users', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });

        Schema::table('employees', function (Blueprint $table) {
            $table->string('email')->nullable(false)->change();
        });
    }
};