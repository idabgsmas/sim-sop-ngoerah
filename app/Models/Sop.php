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
        'id_user', 'created_by', 'updated_by', 'deleted_by'
    ];
    
    protected $casts = [
        'tgl_disahkan' => 'datetime',
        'tgl_berlaku' => 'datetime',
        'tgl_kadaluwarsa' => 'datetime',
        'tgl_review_tahunan' => 'datetime',
    ];

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
