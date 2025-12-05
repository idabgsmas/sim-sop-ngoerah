<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UnitKerja extends Model
{
    use SoftDeletes;
    protected $table = 'tb_unit_kerja';
    protected $primaryKey = 'id_unit_kerja';
    protected $fillable = ['kode_unit_kerja', 'nama_unit', 'id_direktorat', 'email_unit', 'no_telp'];

    public function direktorat()
    {
        return $this->belongsTo(Direktorat::class, 'id_direktorat', 'id_direktorat');
    }
    
    // Relasi ke User (melalui pivot tb_unit_user)
    public function users()
    {
        return $this->belongsToMany(TbUser::class, 'tb_unit_user', 'id_unit_kerja', 'id_user');
    }

    // Relasi ke SOP (SOP yang dimiliki unit ini)
    public function sops()
    {
        return $this->hasMany(Sop::class, 'id_unit_kerja', 'id_unit_kerja');
    }
}
