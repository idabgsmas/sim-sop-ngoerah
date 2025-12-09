<?php

namespace App\Filament\Viewer\Widgets;

use App\Models\Sop;
use Filament\Widgets\ChartWidget;

class SopCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Proporsi Kategori Dokumen';
    protected static ?int $sort = 3; 
    protected static ?string $maxHeight = '350px'; // Agar tidak terlalu besar

    protected function getData(): array
    {
        // Hitung SOP Aktif berdasarkan kategori
        $internal = Sop::where('id_status', 4)->where('kategori_sop', 'SOP Internal')->count();
        $ap = Sop::where('id_status', 4)->where('kategori_sop', 'SOP AP')->count();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah SOP',
                    'data' => [$internal, $ap],
                    'backgroundColor' => [
                        '#3b82f6', // Biru (Internal)
                        '#f59e0b', // Orange (AP - Lebih menonjol)
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['SOP Internal', 'SOP AP (Antar Profesi)'],
        ];
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}