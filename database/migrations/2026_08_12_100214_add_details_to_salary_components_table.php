<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {

            $table->string('category')->nullable()->after('type'); 
            $table->string('calculation_type')->nullable()->after('category'); 
            $table->string('formula_note')->nullable()->after('default_amount'); 
        });
    }


    public function down(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            $table->dropColumn(['category', 'calculation_type', 'formula_note']);
        });
    }
};