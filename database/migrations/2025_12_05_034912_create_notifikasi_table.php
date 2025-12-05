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
        // 10. Tabel Notifikasi
        Schema::create('tb_notifikasi', function (Blueprint $table) {
            $table->integer('id_notifikasi')->autoIncrement();
            $table->integer('id_user'); // Penerima notif
            $table->integer('id_sop')->nullable();
            
            $table->string('judul', 100);
            $table->string('isi_notif', 255);
            $table->boolean('is_read')->default(false);
            
            $table->timestamps();
            
            $table->foreign('id_user')->references('id_user')->on('tb_user')->onDelete('cascade');
            $table->foreign('id_sop')->references('id_sop')->on('tb_sop')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_notifikasi');
    }
};
