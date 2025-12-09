<?php

namespace App\Filament\Verifikator\Widgets;

use App\Models\Sop;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class VerifikatorStats extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // Hitung Status
        $antrean = Sop::where('id_status', Sop::STATUS_BELUM_DIVERIFIKASI)->count(); // ID 2
        $aktif = Sop::where('id_status', Sop::STATUS_AKTIF)->count(); // ID 4
        $revisi = Sop::where('id_status', Sop::STATUS_REVISI)->count(); // ID 3

        return [
            Stat::make('Antrean Verifikasi', $antrean)
                ->description('Dokumen menunggu persetujuan Anda')
                ->descriptionIcon('heroicon-m-inbox-arrow-down')
                ->color($antrean > 0 ? 'danger' : 'success') // Merah jika ada antrean
                ->url(route('filament.verifikator.resources.sops.index')), // Klik langsung ke tabel

            Stat::make('SOP Aktif', $aktif)
                ->description('Total dokumen sah')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),

            Stat::make('Dalam Revisi', $revisi)
                ->description('Sedang diperbaiki unit pengusul')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('warning'),
        ];
    }
}