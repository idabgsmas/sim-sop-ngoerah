<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Direktorat extends Model
{
    use SoftDeletes;
    protected $table = 'tb_direktorat';
    protected $primaryKey = 'id_direktorat';
    protected $fillable = ['kode_direktorat', 'nama_direktorat', 'email_direktorat', 'no_telp'];

    public function unitKerja()
    {
        return $this->hasMany(UnitKerja::class, 'id_direktorat', 'id_direktorat');
    }
}
