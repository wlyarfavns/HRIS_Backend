<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {

            $table->time('time_in')->nullable()->change();
            $table->string('photo_in')->nullable()->change();
            $table->string('latitude_in')->nullable()->change();
            $table->string('longitude_in')->nullable()->change();
            $table->double('distance_in_meters')->nullable()->change();
            $table->boolean('is_mock_location')->nullable()->change();







        });
    }


    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {

            $table->time('time_in')->nullable(false)->change();
            $table->string('photo_in')->nullable(false)->change();
            $table->string('latitude_in')->nullable(false)->change();
            $table->string('longitude_in')->nullable(false)->change();
            $table->double('distance_in_meters')->nullable(false)->change();
            $table->boolean('is_mock_location')->nullable(false)->change();
        });
    }
};