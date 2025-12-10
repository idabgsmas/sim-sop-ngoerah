<?php

namespace App\Filament\Verifikator\Widgets;

use App\Models\Sop;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AntreanTable extends BaseWidget
{
    protected static ?string $heading = 'Antrean Verifikasi (Prioritas)';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sop::query()
                    ->where('id_status', Sop::STATUS_BELUM_DIVERIFIKASI) // Hanya yang belum
                    ->orderBy('updated_at', 'asc') // Yang paling lama menunggu di atas
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('unitKerja.nama_unit')
                    ->label('Unit Pengusul')
                    ->badge(),
                Tables\Columns\TextColumn::make('judul_sop')
                    ->limit(40),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Masuk Sejak')
                    ->since()
                    ->color('danger'),
            ])
            ->actions([
                Tables\Actions\Action::make('proses')
                    ->label('Proses')
                    ->button()
                    ->url(fn (Sop $record) => route('filament.verifikator.resources.sops.view', $record)),
            ]);
    }
}