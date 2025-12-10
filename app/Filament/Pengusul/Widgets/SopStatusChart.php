<?php

namespace App\Filament\Pengusul\Widgets;

use App\Models\Sop;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\Auth;

class SopStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Komposisi Status SOP';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'full'; // Agar memanjang penuh dari kiri ke kanan
    

    protected function getData(): array
    {
        $myUnitIds = Auth::user()->unitKerja->pluck('id_unit_kerja')->toArray();

        // Hitung data per status
        // Format array: [id_status => jumlah]
        $data = Sop::whereIn('id_unit_kerja', $myUnitIds)
            ->selectRaw('count(*) as count, id_status')
            ->groupBy('id_status')
            ->pluck('count', 'id_status')
            ->toArray();

        // Mapping Data agar urut sesuai label (Draft, Proses, Revisi, Aktif)
        // ID Status: 1=Draft, 2=Belum Verif, 3=Revisi, 4=Aktif, 5=Kadaluwarsa
        $counts = [
            $data[4] ?? 0, // Aktif
            $data[2] ?? 0, // Proses Verifikasi
            $data[3] ?? 0, // Revisi
            $data[1] ?? 0, // Draft
            $data[5] ?? 0, // Kadaluwarsa
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah SOP',
                    'data' => $counts,
                    'backgroundColor' => [
                        '#10b981', // Aktif - Hijau
                        '#f59e0b', // Proses - Kuning
                        '#ef4444', // Revisi - Merah
                        '#6b7280', // Draft - Abu
                        '#000000', // Expired - Hitam
                    ],
                ],
            ],
            'labels' => ['Aktif', 'Sedang Diverifikasi', 'Perlu Revisi', 'Draft', 'Kadaluwarsa'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // Bisa ganti 'pie' atau 'bar'
    }
}