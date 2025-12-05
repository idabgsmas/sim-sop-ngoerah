<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $table = 'tb_status';
    protected $primaryKey = 'id_status';
    public $timestamps = false;
    protected $fillable = ['nama_status'];

    // Relasi ke SOP
    public function sops()
    {
        return $this->hasMany(Sop::class, 'id_status', 'id_status');
    }

    // Relasi ke History SOP (Status pada saat history dicatat)
    public function histories()
    {
        return $this->hasMany(HistorySop::class, 'id_status', 'id_status');
    }
}