<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            // Tambahkan kolom remember_token (nullable)
            $table->rememberToken()->after('is_active'); 
        });
    }

    public function down(): void
    {
        Schema::table('tb_user', function (Blueprint $table) {
            $table->dropRememberToken();
        });
    }
};