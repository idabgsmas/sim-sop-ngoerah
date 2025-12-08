<?php

namespace App\Filament\Viewer\Resources;

use App\Filament\Viewer\Resources\SopResource\Pages;
use App\Filament\Viewer\Resources\SopResource\RelationManagers;
use App\Models\Sop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class SopResource extends Resource
{
    protected static ?string $model = Sop::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getEloquentQuery(): Builder
    {
        // Viewer HANYA boleh lihat SOP Aktif
        return parent::getEloquentQuery()->where('id_status', 4); // ID 4 = Aktif
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->columns([
            Tables\Columns\TextColumn::make('judul_sop')->searchable(),
            Tables\Columns\TextColumn::make('nomor_sop'),
            Tables\Columns\TextColumn::make('unitKerja.nama_unit')->label('Unit'),
            Tables\Columns\TextColumn::make('tgl_berlaku')->date(),
        ])
        ->actions([
            // Viewer hanya bisa View & Download
            Tables\Actions\ViewAction::make(),
            Tables\Actions\Action::make('download')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (Sop $record) => asset('storage/' . $record->dokumen_path))
                ->openUrlInNewTab(),
        ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSops::route('/'),
            'create' => Pages\CreateSop::route('/create'),
            'edit' => Pages\EditSop::route('/{record}/edit'),
        ];
    }
}
