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
        // 3. Tabel Status
        Schema::create('tb_status', function (Blueprint $table) {
            $table->integer('id_status')->autoIncrement();
            $table->string('nama_status', 100);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_status');
    }
};
