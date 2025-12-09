<?php
namespace App\Filament\Verifikator\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    public function getColumns(): int | string | array
    {
        return 3; // 3 Kolom agar ringkas
    }

    // (Opsional) Jika Anda ingin urutan widget dikontrol dari sini
    public function getWidgets(): array
    {
        return [
            \App\Filament\Verifikator\Widgets\VerifikatorStats::class,
            \App\Filament\Verifikator\Widgets\AntreanTable::class,
        ];
    }
}