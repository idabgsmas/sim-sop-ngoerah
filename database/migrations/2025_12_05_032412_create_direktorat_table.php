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
        // 1. Tabel Direktorat
        Schema::create('tb_direktorat', function (Blueprint $table) {
            $table->integer('id_direktorat')->autoIncrement();
            $table->char('kode_direktorat', 10)->unique();
            $table->string('nama_direktorat', 100);
            $table->string('email_direktorat', 100)->nullable();
            $table->char('no_telp', 12)->nullable();
            
            // Timestamps Laravel (created_at, updated_at) tetap berguna
            $table->timestamps(); 
            $table->softDeletes(); // deleted_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_direktorat');
    }
};
