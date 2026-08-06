<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->time('standard_in_time')->default('08:00:00')->after('geofence_radius_meters');
            $table->integer('late_tolerance_minutes')->default(15)->after('standard_in_time');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['standard_in_time', 'late_tolerance_minutes']);
        });
    }
};