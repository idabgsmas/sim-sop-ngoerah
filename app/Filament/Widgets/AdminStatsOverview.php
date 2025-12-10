<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use App\Models\TbUser;
use App\Models\UnitKerja;
use App\Models\Direktorat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AdminStatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '30s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        return [
            Stat::make('Total Pengguna', TbUser::count())
                ->description('User terdaftar dalam sistem')
                ->descriptionIcon('heroicon-m-users')
                ->color('primary')
                ->url(route('filament.admin.resources.tb-users.index')),

            Stat::make('Total Unit Kerja', UnitKerja::count())
                ->description('Tersebar di ' . Direktorat::count() . ' Direktorat')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('info')
                ->url(route('filament.admin.resources.unit-kerjas.index')),

            Stat::make('Total Dokumen SOP', Sop::count())
                ->description('Akumulasi semua status')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('success')
                // ->url(route('filament.admin.resources.sops.index')) // Jika Anda membuat resource SOP di admin
                ,
            
            // Opsional: Menampilkan jumlah yang Aktif saja
            Stat::make('SOP Aktif (Valid)', Sop::where('id_status', 4)->count())
                ->description('Dokumen sah yang berlaku')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            
        ];
    }
    
    // Agar tampilan 4 kolom sejajar
    protected function getColumns(): int
    {
        return 4;
    }
}