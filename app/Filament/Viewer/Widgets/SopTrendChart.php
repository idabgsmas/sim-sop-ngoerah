<?php

namespace App\Filament\Viewer\Widgets;

use App\Models\Sop;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SopTrendChart extends ChartWidget
{
    protected static ?string $heading = 'Tren Penerbitan SOP Tahun Ini';
    protected static ?int $sort = 2; // Taruh setelah stats
    protected int | string | array $columnSpan = 'full'; // Lebar penuh agar grafik jelas

    protected function getData(): array
    {
        // Query: Hitung SOP Aktif per Bulan di tahun ini
        $data = Sop::query()
            ->where('id_status', 4) // Hanya Aktif
            ->whereYear('tgl_berlaku', date('Y'))
            ->select(
                DB::raw('MONTH(tgl_berlaku) as month'), 
                DB::raw('count(*) as total')
            )
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month')
            ->toArray();

        // Siapkan array 12 bulan (default 0 jika tidak ada data)
        $monthlyData = [];
        $labels = [];
        for ($i = 1; $i <= 12; $i++) {
            $monthName = Carbon::create()->month($i)->translatedFormat('F'); // Januari, Februari...
            $labels[] = $monthName;
            $monthlyData[] = $data[$i] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'SOP Terbit',
                    'data' => $monthlyData,
                    'borderColor' => '#3b82f6', // Biru
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'fill' => true,
                    'tension' => 0.4, // Garis melengkung halus
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}