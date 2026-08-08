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
        Schema::table('attendances', function (Blueprint $table) {
            // Tambahkan ->nullable()->change() untuk mengedit kolom yang sudah ada
            $table->time('time_in')->nullable()->change();
            $table->string('photo_in')->nullable()->change();
            $table->string('latitude_in')->nullable()->change();
            $table->string('longitude_in')->nullable()->change();
            $table->double('distance_in_meters')->nullable()->change();
            $table->boolean('is_mock_location')->nullable()->change();
            
            // Lakukan hal yang sama jika kolom out sudah ada
            // $table->time('time_out')->nullable()->change();
            // $table->string('photo_out')->nullable()->change();
            // $table->string('latitude_out')->nullable()->change();
            // $table->string('longitude_out')->nullable()->change();
            // $table->double('distance_out_meters')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Kembalikan ke NOT NULL jika migration di-rollback
            $table->time('time_in')->nullable(false)->change();
            $table->string('photo_in')->nullable(false)->change();
            $table->string('latitude_in')->nullable(false)->change();
            $table->string('longitude_in')->nullable(false)->change();
            $table->double('distance_in_meters')->nullable(false)->change();
            $table->boolean('is_mock_location')->nullable(false)->change();
        });
    }
};