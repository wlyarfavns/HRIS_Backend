<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('max_overtime_hours')->default(40)->after('late_tolerance_minutes');
        });

        Schema::table('leave_types', function (Blueprint $table) {
            $table->integer('default_quota')->default(12)->after('is_quota_based');
            $table->boolean('requires_attachment')->default(false)->after('default_quota');
        });
    }


    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('max_overtime_hours');
        });

        Schema::table('leave_types', function (Blueprint $table) {
            $table->dropColumn(['default_quota', 'requires_attachment']);
        });
    }
};
