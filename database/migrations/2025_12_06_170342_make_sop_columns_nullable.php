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
            $table->string('dokumen_path', 255)->nullable()->change();
            $table->enum('kategori_sop', ['SOP Internal', 'SOP AP'])->default('SOP Internal')->nullable()->change();
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
