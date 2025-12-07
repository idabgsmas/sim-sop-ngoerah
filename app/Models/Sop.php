<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sop extends Model
{
    use SoftDeletes;
    protected $table = 'tb_sop';
    protected $primaryKey = 'id_sop';
    protected $guarded = [];
    protected $fillable = [
        'nomor_sop', 'judul_sop', 'deskripsi', 'kategori_sop',
        'dokumen_path', 'tgl_pengesahan', 'tgl_berlaku',
        'tgl_review_tahunan', 'tgl_kadaluwarsa', 'id_status',
        'id_user', 'is_all_units', 'id_unit_kerja', 'created_by', 'updated_by', 'deleted_by'
    ];

    // Helper untuk Logic Status (Sesuaikan ID dengan database tb_status Anda)
    const STATUS_DRAFT = 1; 
    const STATUS_BELUM_DIVERIFIKASI = 2;
    const STATUS_REVISI = 3; 
    const STATUS_AKTIF = 4;
    const STATUS_KADALUWARSA = 5;
    
    protected $casts = [
        'tgl_pengesahan' => 'datetime',
        'tgl_berlaku' => 'datetime',
        'tgl_kadaluwarsa' => 'datetime',
        'tgl_review_tahunan' => 'datetime',
    ];

    // Relasi User Verifikator (Untuk Notifikasi)
    // Asumsi: Kita cari user dengan role Verifikator
    public static function getVerifikators()
    {
        return TbUser::whereHas('role', function($q) {
            $q->where('nama_role', 'Verifikator');
        })->get();
    }

    // Pemilik SOP (Unit)
    public function unitKerja()
    {
        return $this->belongsTo(UnitKerja::class, 'id_unit_kerja', 'id_unit_kerja');
    }

    // Pengupload (User)
    public function uploader()
    {
        return $this->belongsTo(TbUser::class, 'id_user', 'id_user');
    }

    // Status
    public function status()
    {
        return $this->belongsTo(Status::class, 'id_status', 'id_status');
    }

    // History
    public function histories()
    {
        return $this->hasMany(HistorySop::class, 'id_sop', 'id_sop');
    }
    
    // Unit Terkait (Many to Many)
    public function unitTerkait()
    {
        return $this->belongsToMany(UnitKerja::class, 'tb_sop_unit_terkait', 'id_sop', 'id_unit_kerja');
    }
}
