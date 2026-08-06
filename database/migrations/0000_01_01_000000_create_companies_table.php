<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('companies', function (Blueprint $table) {
            $table->id();

            // Identitas & Kontak
            $table->string('name');
            $table->string('subdomain')->unique();
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code', 10)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();

            // Pengaturan Lokasi Absensi Default
            $table->decimal('office_latitude', 10, 7)->nullable();
            $table->decimal('office_longitude', 10, 7)->nullable();
            $table->unsignedInteger('geofence_radius_meters')->default(100);

            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};