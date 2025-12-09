<?php

namespace App\Filament\Viewer\Widgets;

use App\Models\Sop;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ViewerStats extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        // 1. Total Aktif
        $totalAktif = Sop::where('id_status', 4)->count();

        // 2. SOP Baru Bulan Ini (Indikator Update Terbaru)
        $newThisMonth = Sop::where('id_status', 4)
            ->whereMonth('tgl_berlaku', now()->month)
            ->whereYear('tgl_berlaku', now()->year)
            ->count();

        // 3. Total SOP AP (Indikator Kolaborasi Antar Unit)
        $totalAP = Sop::where('id_status', 4)
            ->where('kategori_sop', 'SOP AP')
            ->count();

        return [
            Stat::make('Total SOP Tersedia', $totalAktif)
                ->description('Siap diakses seluruh pegawai')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('primary'),

            Stat::make('Terbit Bulan Ini', $newThisMonth)
                ->description('Dokumen baru / diperbarui')
                ->descriptionIcon('heroicon-m-calendar-days')
                ->color($newThisMonth > 0 ? 'success' : 'gray'), // Hijau jika ada update

            Stat::make('SOP Antar Profesi (AP)', $totalAP)
                ->description('Melibatkan lintas unit')
                ->descriptionIcon('heroicon-m-users')
                ->color('warning'),
        ];
    }
}