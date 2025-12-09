<?php

namespace App\Filament\Pengusul\Widgets;

use App\Models\Sop;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    // Refresh data otomatis setiap 30 detik
    protected static ?string $pollingInterval = '30s';
    
    // Agar widget ini memanjang penuh (layout responsif)
    protected int | string | array $columnSpan = 'full';

    protected function getColumns(): int
    {
        return 4; // Memaksa menjadi 4 kolom
    }

    

    protected function getStats(): array
    {
        $user = Auth::user();
        $myUnitIds = $user->unitKerja->pluck('id_unit_kerja')->toArray();

        // 1. Total SOP Unit (Semua Status)
        $totalSop = Sop::whereIn('id_unit_kerja', $myUnitIds)->count();

        // 2. Perlu Revisi (Status ID 3)
        $revisiSop = Sop::whereIn('id_unit_kerja', $myUnitIds)
            ->where('id_status', Sop::STATUS_REVISI) // ID 3
            ->count();

        // 3. Menunggu Verifikasi (Status ID 2)
        $pendingSop = Sop::whereIn('id_unit_kerja', $myUnitIds)
            ->where('id_status', Sop::STATUS_BELUM_DIVERIFIKASI) // ID 2
            ->count();

        // 4. AKAN KADALUWARSA (Logic Baru)
        // Kriteria: Status Aktif (4) DAN Tgl Kadaluwarsa ada di rentang Hari Ini s.d. 90 Hari ke depan
        $expiringSop = Sop::whereIn('id_unit_kerja', $myUnitIds)
            ->where('id_status', Sop::STATUS_AKTIF) // Hanya cek yang aktif
            ->whereBetween('tgl_kadaluwarsa', [now(), now()->addDays(90)])
            ->count();

        return [
            Stat::make('Total Dokumen', $totalSop)
                ->description('Semua SOP unit Anda')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('primary'),

            Stat::make('Perlu Revisi', $revisiSop)
                ->description('Segera perbaiki')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('danger') // Merah (Urgent)
                ->url(route('filament.pengusul.resources.sops.index', ['tableFilters[id_status][value]' => 3])),

            Stat::make('Akan Kadaluwarsa', $expiringSop) // <--- Widget Baru
                ->description('< 90 Hari lagi')
                ->descriptionIcon('heroicon-m-clock')
                ->color($expiringSop > 0 ? 'warning' : 'success') // Kuning jika ada, Hijau jika aman
                ->url(route('filament.pengusul.resources.sops.index')), // Bisa diarahkan ke filter khusus jika mau

            Stat::make('Proses Verifikasi', $pendingSop)
                ->description('Di meja bagian hukum')
                ->descriptionIcon('heroicon-m-arrow-path')
                ->color('gray'),
        ];
    }
}