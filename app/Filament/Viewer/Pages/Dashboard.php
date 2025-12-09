<?php
namespace App\Filament\Viewer\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | string | array
    {
        return 1; // 3 Kolom agar ringkas
    }

    // (Opsional) Jika Anda ingin urutan widget dikontrol dari sini
    public function getWidgets(): array
    {
        return [
            \App\Filament\Viewer\Widgets\ViewerStats::class,
            \App\Filament\Viewer\Widgets\LatestSopTable::class,
            \App\Filament\Viewer\Widgets\SopCategoryChart::class,
            \App\Filament\Viewer\Widgets\SopDirektoratChart::class,
            \App\Filament\Viewer\Widgets\SopTrendChart::class,

        ];
    }
}