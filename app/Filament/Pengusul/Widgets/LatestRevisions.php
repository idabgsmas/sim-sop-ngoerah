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
    protected static ?int $sort = 3; // Urutan tampilan setelah Stats

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
                        ->limit(40)
                        ->tooltip(fn ($record) => $record->judul_sop),

                    Tables\Columns\TextColumn::make('nomor_sop')
                    ->label('No. Dokumen'),

                    Tables\Columns\TextColumn::make('catatan_terakhir')
                    ->label('Catatan Revisi')
                    ->state(function ($record) {
                        // Ambil history terakhir dengan status Revisi (ID 3)
                        $history = $record->histories()
                            ->where('id_status', 3)
                            ->latest('created_at')
                            ->first();
                        
                        return $history ? $history->keterangan_perubahan : '-';
                    })
                    ->wrap() // Agar teks panjang turun ke bawah (tidak melebar)
                    ->limit(60) // Batasi panjang teks
                    ->color('danger') // Warna merah agar terlihat warning
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small) // Ukuran font kecil agar muat
                    ->tooltip(fn (Tables\Columns\TextColumn $column) => $column->getState()), // Hover untuk baca selengkapnya
                // ----------------------------------

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Tanggal Revisi')
                    ->since()
                    ->color('gray')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small),

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