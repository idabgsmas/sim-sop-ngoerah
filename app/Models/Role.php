<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'tb_role';
    protected $primaryKey = 'id_role';
    public $timestamps = false; // Tidak ada created_at/updated_at di PDM untuk tabel ini
    protected $fillable = ['nama_role', 'deskripsi_role'];

    // Relasi ke User
    public function users()
    {
        return $this->hasMany(TbUser::class, 'id_role', 'id_role');
    }
}
