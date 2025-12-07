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
        Schema::table('tb_sop', function (Blueprint $table) {
            $table->integer('id_unit_kerja'); // Menambahkan kolom id_unit_kerja
            $table->foreign('id_unit_kerja')->references('id_unit_kerja')->on('tb_unit_kerja');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_sop', function (Blueprint $table) {
            //
        });
    }
};
