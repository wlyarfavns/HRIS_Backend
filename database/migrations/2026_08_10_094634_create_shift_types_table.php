<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shift_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('code', 5);       // P, S, M, L
            $table->string('name');          // Shift Pagi, dst
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->boolean('is_cross_day')->default(false); // shift malam lewat H+1
            $table->boolean('is_off')->default(false);       // untuk kode 'L' (libur)
            $table->string('color', 7)->default('#059669');  // hex, dipakai di UI
            $table->string('description')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shift_types');
    }
};