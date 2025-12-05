<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Filament\Models\Contracts\FilamentUser; // Penting untuk Filament

class TbUser extends Authenticatable
{
    use Notifiable, SoftDeletes;

    protected $table = 'tb_user';       // Custom Table Name
    protected $primaryKey = 'id_user';  // Custom PK

    protected $fillable = [
        'nama_lengkap', 'username', 'email', 'password', 'id_role', 'id_direktorat', 'is_active'
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'password' => 'hashed',
    ];

    // --- LOGIKA PINTU MASUK (MULTI PANEL) ---
    public function canAccessPanel(Panel $panel): bool
    {
        // 1. Cek apakah user aktif?
        if (!$this->is_active) {
            return false;
        }

        // 2. Logika Panel ADMIN (Super Admin & Verifikator)
        if ($panel->getId() === 'admin') {
            return $this->role === 'Administrator';
        }

        // 3. Logika Panel PENGUSUL
        if ($panel->getId() === 'pengusul') {
            return $this->role === 'Pengusul';
        }

        // 4. Logika Panel DIREKSI
        if ($panel->getId() === 'direksi') {
            return $this->role === 'Direksi';
        }

        // 5. Logika Panel VERIFIKATOR
        if ($panel->getId() === 'verifikator') {
            return $this->role === 'Verifikator';
        }

        // 6. Logika Panel Viewer
        if ($panel->getId() === 'viewer') {
            return $this->role === 'Viewer';
        }

        // Default: Tolak akses
        return false;
    }

    // --- AGAR NAMA MUNCUL DI POJOK KANAN ---
    public function getFilamentName(): string
    {
        return $this->nama_lengkap;
    }
    
    // Relasi Role
    public function role()
    {
        return $this->belongsTo(Role::class, 'id_role', 'id_role');
    }

    public function direktorat()
    {
        return $this->belongsTo(Direktorat::class, 'id_direktorat', 'id_direktorat');
    }

    // Relasi Unit Kerja (Many to Many)
    public function unitKerja()
    {
        return $this->belongsToMany(UnitKerja::class, 'tb_unit_user', 'id_user', 'id_unit_kerja');
    }
}
