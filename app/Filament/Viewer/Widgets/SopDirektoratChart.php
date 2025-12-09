<?php

namespace App\Filament\Viewer\Widgets;

use App\Models\Sop;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SopDirektoratChart extends ChartWidget
{
    protected static ?string $heading = 'Sebaran SOP per Direktorat';
    protected static ?int $sort = 3;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Query Agregat: Join SOP -> Unit -> Direktorat, lalu hitung jumlahnya
        $data = Sop::query()
            ->where('id_status', 4) // Hanya yang Aktif
            ->join('tb_unit_kerja', 'tb_sop.id_unit_kerja', '=', 'tb_unit_kerja.id_unit_kerja')
            ->join('tb_direktorat', 'tb_unit_kerja.id_direktorat', '=', 'tb_direktorat.id_direktorat')
            ->select('tb_direktorat.nama_direktorat', DB::raw('count(*) as total'))
            ->groupBy('tb_direktorat.nama_direktorat')
            ->pluck('total', 'nama_direktorat')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah SOP',
                    'data' => array_values($data),
                    'backgroundColor' => [
                        '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', 
                        '#6366f1', '#ec4899', '#14b8a6'
                    ], // Warna-warni
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar'; // Bisa ganti 'pie' atau 'doughnut'
    }
}