<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('overtime_formula')->default('1/173 × Gaji Pokok × Jam')->after('late_tolerance_minutes');
        });
    }


    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn('overtime_formula');
        });
    }
};
