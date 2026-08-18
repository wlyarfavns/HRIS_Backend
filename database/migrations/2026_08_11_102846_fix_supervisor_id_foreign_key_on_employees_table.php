<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->dropForeign(['supervisor_id']);


            $table->foreign('supervisor_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {

            $table->dropForeign(['supervisor_id']);

            $table->foreign('supervisor_id')
                  ->references('id')
                  ->on('employees')
                  ->nullOnDelete();
        });
    }
};