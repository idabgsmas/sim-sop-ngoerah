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
        // 7. Tabel SOP
        Schema::create('tb_sop', function (Blueprint $table) {
            $table->integer('id_sop')->autoIncrement();
            $table->integer('id_user'); // Pembuat (Uploader)
            $table->integer('id_status');
            // SARAN PERBAIKAN: Tambah id_unit_kerja agar SOP terikat ke Unit, bukan cuma ke User
            // $table->integer('id_unit_kerja'); 
            
            $table->string('nomor_sop', 100)->unique()->nullable(); // Nullable dulu karena draft mungkin belum ada nomor
            $table->string('judul_sop', 100);
            $table->string('deskripsi', 255)->nullable();
            $table->enum('kategori_sop', ['SOP Internal', 'SOP AP'])->default('SOP Internal');
            $table->string('dokumen_path', 255);
            
            $table->dateTime('tgl_pengesahan')->nullable();
            $table->dateTime('tgl_berlaku')->nullable();
            $table->dateTime('tgl_kadaluwarsa')->nullable();
            $table->dateTime('tgl_review_tahunan')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            // Foreign Keys
            $table->foreign('id_user')->references('id_user')->on('tb_user');
            $table->foreign('id_status')->references('id_status')->on('tb_status');
            // $table->foreign('id_unit_kerja')->references('id_unit_kerja')->on('tb_unit_kerja');
        });

        // 8. Tabel History SOP (Versioning)
        Schema::create('tb_history_sop', function (Blueprint $table) {
            $table->integer('id_history_sop')->autoIncrement();
            $table->integer('id_sop');
            $table->integer('id_user'); // User yang melakukan perubahan
            $table->integer('id_status');
            
            $table->text('keterangan_perubahan')->nullable();
            $table->string('dokumen_path', 255)->nullable(); // File versi lama
            
            
            $table->timestamps(); 
            $table->softDeletes();
            
            $table->foreign('id_user')->references('id_user')->on('tb_user');
            $table->foreign('id_sop')->references('id_sop')->on('tb_sop')->onDelete('cascade');
            $table->foreign('id_status')->references('id_status')->on('tb_status');
        });

        // 9. Tabel SOP Unit Terkait (Pivot)
        Schema::create('tb_sop_unit_terkait', function (Blueprint $table) {
            $table->integer('id_sop_unit_terkait')->autoIncrement();
            $table->integer('id_sop');
            $table->integer('id_unit_kerja');
            
            $table->foreign('id_sop')->references('id_sop')->on('tb_sop')->onDelete('cascade');
            $table->foreign('id_unit_kerja')->references('id_unit_kerja')->on('tb_unit_kerja')->onDelete('cascade');
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tb_sop');
        Schema::dropIfExists('tb_history_sop');
        Schema::dropIfExists('tb_sop_unit_terkait');
    }
};
