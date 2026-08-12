<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE leave_requests
            MODIFY COLUMN status ENUM('pending', 'pending_spv', 'pending_hr', 'approved', 'rejected') NOT NULL DEFAULT 'pending_spv'
        ");

        DB::table('leave_requests')->where('status', 'pending')->update(['status' => 'pending_spv']);
        DB::statement("
            ALTER TABLE leave_requests
            MODIFY COLUMN status ENUM('pending_spv', 'pending_hr', 'approved', 'rejected') NOT NULL DEFAULT 'pending_spv'
        ");
    }

    public function down(): void
    {
        // Lakukan hal yang sama secara terbalik jika terjadi rollback (php artisan migrate:rollback)
        DB::statement("
            ALTER TABLE leave_requests
            MODIFY COLUMN status ENUM('pending', 'pending_spv', 'pending_hr', 'approved', 'rejected') NOT NULL DEFAULT 'pending'
        ");

        // Kembalikan semua yang masih nyangkut di SPV atau HR menjadi pending biasa
        DB::table('leave_requests')
            ->whereIn('status', ['pending_spv', 'pending_hr'])
            ->update(['status' => 'pending']);

        DB::statement("
            ALTER TABLE leave_requests
            MODIFY COLUMN status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending'
        ");
    }
};