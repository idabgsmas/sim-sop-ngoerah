<?php

namespace App\Filament\Pengusul\Widgets;

use App\Models\Sop;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    // Refresh otomatis setiap 30 detik
    protected static ?string $pollingInterval = '30s';
    
    // Lebar penuh
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 2;

    // ATUR GRID: 3 Kolom agar menjadi 2 Baris yang rapi (Total 6 Widget)
    protected function getColumns(): int
    {
        return 3;
    }

    protected function getStats(): array
    {
        $user = Auth::user();
        $myUnitIds = $user->unitKerja->pluck('id_unit_kerja')->toArray();

        // 1. Total Dokumen (Semua)
        $totalSop = Sop::whereIn('id_unit_kerja', $myUnitIds)->count();

        // 2. SOP AKTIF (Baru)
        $aktifSop = Sop::whereIn('id_unit_kerja', $myUnitIds)
            ->where('id_status', Sop::STATUS_AKTIF) // ID 4
            ->count();

        // 3. SOP KADALUWARSA (Baru)
        // Kita hitung yang statusnya ID 5 ATAU yang tanggalnya sudah lewat (untuk keamanan)
        $expiredSop = Sop::whereIn('id_unit_kerja', $myUnitIds)
            ->where(function ($q) {
                $q->where('id_status', Sop::STATUS_KADALUWARSA) // ID 5
                  ->orWhere('tgl_kadaluwarsa', '<', now());     // Atau tgl sudah lewat
            })
            ->count();

        // 4. Perlu Revisi (Action Required)
        $revisiSop = Sop::whereIn('id_unit_kerja', $myUnitIds)
            ->where('id_status', Sop::STATUS_REVISI) // ID 3
            ->count();

        // 5. Menunggu Verifikasi
        $pendingSop = Sop::whereIn('id_unit_kerja', $myUnitIds)
            ->where('id_status', Sop::STATUS_BELUM_DIVERIFIKASI) // ID 2
            ->count();

        // 6. Akan Kadaluwarsa (< 90 Hari)
        $warningSop = Sop::whereIn('id_unit_kerja', $myUnitIds)
            ->where('id_status', Sop::STATUS_AKTIF) // Hanya yang aktif
            ->whereBetween('tgl_kadaluwarsa', [now(), now()->addDays(90)])
            ->count();

        return [
            // BARIS 1: Status Dokumen
            Stat::make('Total Dokumen', $totalSop)
                ->description('Semua aset dokumen unit')
                ->descriptionIcon('heroicon-m-document-duplicate')
                ->color('primary'),

            Stat::make('SOP Aktif', $aktifSop)
                ->description('Dokumen sah & berlaku')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success') // Hijau
                ->url(route('filament.pengusul.resources.sops.index', ['tableFilters[id_status][value]' => 4])),

            Stat::make('Sudah Kadaluwarsa', $expiredSop)
                ->description('Tidak berlaku lagi')
                ->descriptionIcon('heroicon-m-archive-box-x-mark')
                ->color('danger') // Merah
                ->chart([5, 2, 8, 1, 0]) // Dekorasi chart
                ->url(route('filament.pengusul.resources.sops.index', ['tableFilters[id_status][value]' => 5])),

            // BARIS 2: Action Items (Perlu Tindakan)
            Stat::make('Perlu Revisi', $revisiSop)
                ->description('Segera perbaiki')
                ->descriptionIcon('heroicon-m-pencil-square')
                ->color('danger')
                ->url(route('filament.pengusul.resources.sops.index', ['tableFilters[id_status][value]' => 3])),

            Stat::make('Proses Verifikasi', $pendingSop)
                ->description('Menunggu persetujuan')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->url(route('filament.pengusul.resources.sops.index', ['tableFilters[id_status][value]' => 2])), // Kuning

            Stat::make('Akan Kadaluwarsa', $warningSop)
                ->description('< 90 hari lagi (Warning)')
                ->descriptionIcon('heroicon-m-bell-alert')
                ->color($warningSop > 0 ? 'warning' : 'success'),
        ];
    }
}