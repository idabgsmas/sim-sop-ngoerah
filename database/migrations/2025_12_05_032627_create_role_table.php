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
        // 2. Tabel Role
        Schema::create('tb_role', function (Blueprint $table) {
            $table->integer('id_role')->autoIncrement();
            $table->string('nama_role', 50);
            $table->string('deskripsi_role', 255)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_role');
    }
};
