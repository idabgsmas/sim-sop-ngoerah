<?php

namespace App\Filament\Pengusul\Widgets;

use App\Models\Sop;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class LatestRevisions extends BaseWidget
{
    // Judul Widget
    protected int | string | array $columnSpan = 'full'; // Agar lebar penuh
    protected static ?string $heading = 'Perlu Segera Direvisi';
    protected static ?int $sort = 2; // Urutan tampilan setelah Stats

    public function table(Table $table): Table
    {
        return $table
            ->query(function () {
                // Ambil Unit Kerja User
                $myUnitIds = Auth::user()->unitKerja->pluck('id_unit_kerja')->toArray();

                return Sop::query()
                    ->whereIn('id_unit_kerja', $myUnitIds)
                    ->where('id_status', 3) // ID 3 = Status Revisi
                    ->latest('updated_at')
                    ->limit(5);
            })
            ->columns([
                Tables\Columns\TextColumn::make('judul_sop')
                    ->label('Judul SOP')
                    ->searchable()
                    ->limit(40),
                
                Tables\Columns\TextColumn::make('nomor_sop')
                    ->label('No. Dokumen'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Revisi')
                    ->since() // Tampil "2 hours ago"
                    ->color('danger'),
            ])
            ->actions([
                // Tombol langsung menuju halaman Edit
                Tables\Actions\Action::make('perbaiki')
                    ->label('Perbaiki')
                    ->icon('heroicon-m-pencil-square')
                    ->url(fn (Sop $record) => route('filament.pengusul.resources.sops.edit', $record))
                    ->button()
                    ->color('warning'),
            ])
            ->paginated(false); // Matikan pagination biar ringkas
    }
}