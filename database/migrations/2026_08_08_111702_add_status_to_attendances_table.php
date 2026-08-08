<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Tambahkan kolom status, defaultnya adalah 'alpa' jika tidak ada aksi
            $table->string('status', 50)->default('alpa')->after('time_in');
        });
    }

    public function down()
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
};
