<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TbNotifikasi extends Model
{
    protected $table = 'tb_notifikasi';
    protected $primaryKey = 'id_notifikasi';
    protected $guarded = ['id_user', 'id_sop', 'judul', 'isi_notif', 'is_read'];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // Penerima Notifikasi
    public function user()
    {
        return $this->belongsTo(TbUser::class, 'id_user', 'id_user');
    }

    // SOP Terkait (jika ada)
    public function sop()
    {
        return $this->belongsTo(Sop::class, 'id_sop', 'id_sop');
    }
}