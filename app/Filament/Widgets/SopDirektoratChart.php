<?php

namespace App\Filament\Widgets;

use App\Models\Sop;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class SopDirektoratChart extends ChartWidget
{
    protected static ?string $heading = 'Distribusi SOP per Direktorat';
    protected static ?int $sort = 2;
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Query: Hitung SOP (Semua Status) per Direktorat
        $data = Sop::query()
            ->join('tb_unit_kerja', 'tb_sop.id_unit_kerja', '=', 'tb_unit_kerja.id_unit_kerja')
            ->join('tb_direktorat', 'tb_unit_kerja.id_direktorat', '=', 'tb_direktorat.id_direktorat')
            ->select('tb_direktorat.nama_direktorat', DB::raw('count(*) as total'))
            ->groupBy('tb_direktorat.nama_direktorat')
            ->orderBy('total', 'desc') // Urutkan dari yang terbanyak
            ->pluck('total', 'nama_direktorat')
            ->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Total Dokumen',
                    'data' => array_values($data),
                    'backgroundColor' => '#3b82f6', // Biru
                    'borderRadius' => 4,
                ],
            ],
            'labels' => array_keys($data),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}