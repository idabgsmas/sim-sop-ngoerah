<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class TopUnitChart extends ChartWidget
{
    protected static ?string $heading = 'Top 5 Unit Kerja Paling Produktif';
    protected static ?int $sort = 4; // Taruh di urutan bawah
    protected static ?string $maxHeight = '300px';
    protected int | string | array $columnSpan = 'full'; // Lebar penuh agar nama unit terbaca jelas

    protected function getData(): array
    {
        // Query: Hitung SOP per Unit, Urutkan Terbanyak, Ambil 5 teratas
        $data = Sop::query()
            ->join('tb_unit_kerja', 'tb_sop.id_unit_kerja', '=', 'tb_unit_kerja.id_unit_kerja')
            ->select('tb_unit_kerja.nama_unit', DB::raw('count(*) as total'))
            ->groupBy('tb_unit_kerja.nama_unit')
            ->orderBy('total', 'desc')
            ->limit(5)
            ->pluck('total', 'nama_unit')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Dokumen',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#3b82f6', // Biru
                        '#8b5cf6', // Ungu
                        '#ec4899', // Pink
                        '#10b981', // Hijau
                        '#f59e0b', // Kuning
                    ], 
                    'borderRadius' => 5,
                    'barThickness' => 30, // Ketebalan batang
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bar chart
    }

    // OPSI TAMBAHAN: Ubah jadi Horizontal Bar agar nama unit panjang tidak terpotong
    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y', // 'y' membuat bar chart jadi horizontal
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false,
                    ],
                ],
            ],
            'plugins' => [
                'legend' => [
                    'display' => false, // Sembunyikan legend karena sudah jelas
                ],
            ],
        ];
    }
}