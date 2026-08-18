<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE employees MODIFY COLUMN employment_status ENUM('PKWT', 'PKWTT', 'Probation', 'Internship') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverting this might truncate data if there are already Probation or Internship records.
        // It's safer not to strictly revert the enum to a smaller size in production if data exists.
        DB::statement("ALTER TABLE employees MODIFY COLUMN employment_status ENUM('PKWT', 'PKWTT') NOT NULL");
    }
};
