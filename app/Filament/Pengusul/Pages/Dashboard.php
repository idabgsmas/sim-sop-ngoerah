<?php

namespace App\Filament\Pengusul\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    // Mengatur jumlah kolom grid
    public function getColumns(): int | string | array
    {
        return 2;
    }
    
    // (Opsional) Jika Anda ingin urutan widget dikontrol dari sini
    public function getWidgets(): array
    {
        return [
            \App\Filament\Pengusul\Widgets\StatsOverview::class,
            \App\Filament\Pengusul\Widgets\LatestRevisions::class,
            \App\Filament\Pengusul\Widgets\SopStatusChart::class,
        ];
    }
}