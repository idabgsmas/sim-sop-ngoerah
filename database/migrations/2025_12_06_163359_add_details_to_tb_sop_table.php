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
            $table->boolean('is_all_units')->default(false)->after('kategori_sop');
            // Pastikan kolom ini ada, jika belum buat migrationnya
            // $table->string('kategori_sop')->nullable(); 
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
