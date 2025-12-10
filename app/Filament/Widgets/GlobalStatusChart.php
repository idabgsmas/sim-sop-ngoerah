<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class GlobalStatusChart extends ChartWidget
{
    protected static ?string $heading = 'Status Dokumen Global';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Query: Hitung SOP berdasarkan Status
        $data = Sop::query()
            ->join('tb_status', 'tb_sop.id_status', '=', 'tb_status.id_status')
            ->select('tb_status.nama_status', DB::raw('count(*) as total'))
            ->groupBy('tb_status.nama_status')
            ->pluck('total', 'nama_status')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Dokumen',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#10b981', // Aktif - Hijau
                        '#f59e0b', // Belum Verif - Kuning
                        '#ef4444', // Revisi - Merah
                        '#6b7280', // Draft - Abu
                        '#000000', // Expired - Hitam
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'doughnut'; // atau 'pie'
    }
}