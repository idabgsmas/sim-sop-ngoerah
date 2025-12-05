<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HistorySop extends Model
{
    protected $table = 'tb_history_sop';
    protected $primaryKey = 'id_history_sop';
    
    // Kita manfaatkan timestamps Laravel untuk mengisi 'modified_at'
    // Tapi karena nama kolomnya 'modified_at', kita perlu kustomisasi
    // const UPDATED_AT = null; // Tidak ada updated_at standar
    // const CREATED_AT = 'modified_at'; // Gunakan created_at Laravel untuk mengisi modified_at saat insert
    
    protected $fillable = ['id_sop', 'id_user', 'id_status', 'keterangan_perubahan', 'dokumen_path'];

    // protected $casts = [
    //     'modified_at' => 'datetime',
    // ];

    public function sop()
    {
        return $this->belongsTo(Sop::class, 'id_sop', 'id_sop');
    }

    public function status()
    {
        return $this->belongsTo(Status::class, 'id_status', 'id_status');
    }

    public function user()
    {
        return $this->belongsTo(TbUser::class, 'id_user', 'id_user');
    }
}