<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {

            if (!Schema::hasColumn('attendances', 'status')) {
                $table->string('status')->nullable()->after('is_mock_location');

            }
            if (!Schema::hasColumn('attendances', 'late_minutes')) {
                $table->unsignedInteger('late_minutes')->nullable()->after('status');
            }


            if (!Schema::hasColumn('attendances', 'photo_out')) {
                $table->string('photo_out')->nullable()->after('photo_in');
            }
            if (!Schema::hasColumn('attendances', 'latitude_out')) {
                $table->decimal('latitude_out', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'longitude_out')) {
                $table->decimal('longitude_out', 10, 7)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'distance_out_meters')) {
                $table->decimal('distance_out_meters', 8, 2)->nullable();
            }
            if (!Schema::hasColumn('attendances', 'is_mock_location_out')) {
                $table->boolean('is_mock_location_out')->nullable();
            }


            if (!Schema::hasColumn('attendances', 'note')) {
                $table->string('note')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $columns = [
                'status', 'late_minutes', 'photo_out', 'latitude_out',
                'longitude_out', 'distance_out_meters', 'is_mock_location_out', 'note',
            ];
            foreach ($columns as $col) {
                if (Schema::hasColumn('attendances', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};