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
        Schema::table('salary_components', function (Blueprint $table) {
            // Pengelompokan tampilan, terpisah dari 'type' (earning/deduction) yang dipakai kalkulasi payroll.
            $table->string('category')->nullable()->after('type'); // Pendapatan Tetap | Pendapatan Variabel | Potongan
            $table->string('calculation_type')->nullable()->after('category'); // Fixed, Harian, Rumus Depnaker, Persentase, dst
            $table->string('formula_note')->nullable()->after('default_amount'); // "1/173 × Gaji Pokok × Jam"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('salary_components', function (Blueprint $table) {
            $table->dropColumn(['category', 'calculation_type', 'formula_note']);
        });
    }
};