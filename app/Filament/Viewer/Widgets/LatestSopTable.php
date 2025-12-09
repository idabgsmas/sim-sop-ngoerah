<?php

namespace App\Filament\Viewer\Widgets;

use App\Models\Sop;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestSopTable extends BaseWidget
{
    protected static ?string $heading = 'SOP Terbaru Terbit';
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sop::query()
                    ->where('id_status', Sop::STATUS_AKTIF)
                    ->latest('updated_at') // Yang baru aktif paling atas
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('judul_sop')->label('Judul')->searchable(),
                Tables\Columns\TextColumn::make('nomor_sop')->label('No. Dokumen'),
                Tables\Columns\TextColumn::make('tgl_berlaku')->date()->label('TMT'),
            ])
            ->actions([
                // Arahkan ke View (bukan Edit)
                Tables\Actions\ViewAction::make()
                    ->url(fn (Sop $record) => route('filament.viewer.resources.sops.view', $record)), 
            ]);
    }
}
