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
        // 4. Tabel Unit Kerja
        Schema::create('tb_unit_kerja', function (Blueprint $table) {
            $table->integer('id_unit_kerja')->autoIncrement();
            $table->integer('id_direktorat'); // FK Definition di bawah
            $table->char('kode_unit_kerja', 10)->unique();
            
            $table->string('nama_unit', 100);
            $table->string('email_unit', 100)->nullable();
            $table->char('no_telp', 12)->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('id_direktorat')->references('id_direktorat')->on('tb_direktorat')
                ->onDelete('cascade')->onUpdate('restrict');
                
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_unit');
    }
};
