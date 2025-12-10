<?php

namespace App\Filament\Pengusul\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class UserInfoWidget extends Widget
{
    // Arahkan ke file view yang akan kita buat
    protected static string $view = 'filament.pengusul.widgets.user-info-widget';

    // Lebar penuh agar terlihat seperti banner header
    protected int | string | array $columnSpan = 'full';

    // Urutan paling atas (angka negatif agar mendahului widget lain)
    protected static ?int $sort = 1;
    
    // Kirim data user ke view
    protected function getViewData(): array
    {
        $user = Auth::user();
        
        return [
            'nama' => $user->nama_lengkap,
            // Ambil unit kerja pertama (jika punya banyak)
            'unit' => $user->unitKerja->first()?->nama_unit ?? '-',
            // Ambil direktorat dari relasi (pastikan relasi di model benar)
            'direktorat' => $user->unitKerja->first()?->direktorat?->nama_direktorat 
                            ?? $user->direktorat?->nama_direktorat 
                            ?? '-',
        ];
    }
}