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
        // 5. Modifikasi Tabel User (tb_user)
        // Catatan: Laravel sudah punya tabel 'users', kita sesuaikan.
        // Schema::rename('users', 'tb_user');
        Schema::create('tb_user', function (Blueprint $table) {
            // Rename ID bawaan
            $table->integer('id_user')->autoIncrement();
            $table->integer('id_direktorat')->nullable(); // FK ke tb_direktorat
            $table->integer('id_role')->nullable(); // FK ke tb_role
            $table->string('username', 100)->unique();
            $table->string('email', 150)->unique();
            $table->string('nama_lengkap', 255);
            $table->string('password', 255);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();
            
            // Relasi
            $table->foreign('id_role')->references('id_role')->on('tb_role');
            $table->foreign('id_direktorat')->references('id_direktorat')->on('tb_direktorat')
                ->onDelete('set null')->onUpdate('cascade');
        });

         // 6. Tabel Pivot Unit-User (Many to Many)
        Schema::create('tb_unit_user', function (Blueprint $table) {
            $table->integer('id_unit_user')->autoIncrement();
            $table->integer('id_user');
            $table->integer('id_unit_kerja');
            
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
            $table->foreign('id_unit_kerja')->references('id_unit_kerja')->on('tb_unit_kerja')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_user');
        Schema::dropIfExists('tb_unit_user');
    }
};
