<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['activation_token', 'activation_expired_at']);
        });
    }


    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->string('activation_token', 60)->nullable();
            $table->timestamp('activation_expired_at')->nullable();
        });
    }
};