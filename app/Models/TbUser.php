<?php

namespace App\Models;

use Filament\Panel;
use Filament\Models\Contracts\HasName;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\HasDatabaseNotifications;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser; // Penting untuk Filament


class TbUser extends Authenticatable implements FilamentUser, HasName
{
    use Notifiable, SoftDeletes, HasDatabaseNotifications;

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

        // Ambil nama role dari relasi (Pastikan user punya role)
        // Menggunakan optional() agar tidak error jika id_role null
        $roleName = optional($this->role)->nama_role;

        // 2. Logika Panel ADMIN (Super Admin & Verifikator)
        
        if ($panel->getId() === 'admin') {
            return $roleName === 'Administrator';
        }

        // 3. Logika Panel PENGUSUL
        if ($panel->getId() === 'pengusul') {
            return $roleName === 'Pengusul';
        }

        // 4. Logika Panel DIREKSI
        if ($panel->getId() === 'direksi') {
            return $roleName === 'Direksi';
        }

        // 5. Logika Panel VERIFIKATOR
        if ($panel->getId() === 'verifikator') {
            return $roleName === 'Verifikator';
        }

        // 6. Logika Panel Viewer
        if ($panel->getId() === 'viewer') {
            return $roleName === 'Viewer';
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
