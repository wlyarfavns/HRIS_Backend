<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('employees', function (Blueprint $table) {
            // 1. Hapus ikatan lama (yang mengarah ke tabel employees)
            $table->dropForeign(['supervisor_id']);
            
            // 2. Buat ikatan baru (yang mengarah ke tabel users)
            $table->foreign('supervisor_id')
                  ->references('id')
                  ->on('users')
                  ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            // Mengembalikan ke kondisi semula jika di-rollback
            $table->dropForeign(['supervisor_id']);
            
            $table->foreign('supervisor_id')
                  ->references('id')
                  ->on('employees')
                  ->nullOnDelete();
        });
    }
};