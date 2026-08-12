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
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->string('attachment')->nullable();          // path file lampiran
            $table->unsignedBigInteger('approved_by')->nullable(); // id user yang approve/reject
            $table->timestamp('approved_at')->nullable();      // waktu diproses
            $table->text('rejection_reason')->nullable();      // alasan penolakan
        });
    }

    public function down(): void
    {
        Schema::table('leave_requests', function (Blueprint $table) {
            $table->dropColumn(['attachment', 'approved_by', 'approved_at', 'rejection_reason']);
        });
    }
};
