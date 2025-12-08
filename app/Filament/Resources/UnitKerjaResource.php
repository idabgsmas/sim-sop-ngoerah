<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UnitKerjaResource\Pages;
use App\Filament\Resources\UnitKerjaResource\RelationManagers;
use App\Models\UnitKerja;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UnitKerjaResource extends Resource
{
    protected static ?string $model = UnitKerja::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    protected static ?string $recordTitleAttribute = 'Unit Kerja';

    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 2;

    protected static ?string $navigationLabel = 'Unit Kerja';
    protected static ?string $modelLabel = 'Unit Kerja';
    protected static ?string $pluralModelLabel = 'Unit Kerja';
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Unit Kerja')->schema([
                Forms\Components\TextInput::make('kode_unit_kerja')
                    ->label('Kode Unit Kerja')
                    ->required()
                    ->maxLength(5)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('nama_unit')
                    ->label('Nama Unit Kerja')
                    ->required()
                    ->maxLength(100),
                Forms\Components\Select::make('id_direktorat')
                    ->relationship('direktorat', 'nama_direktorat')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->label('Direktorat Induk'),
                Forms\Components\TextInput::make('email_unit')
                    ->label('Email Unit Kerja')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('no_telp')
                    ->label('No. Telepon Unit Kerja')
                    ->tel()
                    ->maxLength(12)
                    ->minLength(12),
                ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_unit_kerja')
                    ->label('Kode Unit Kerja')
                    ->sortable(),
                Tables\Columns\TextColumn::make('direktorat.nama_direktorat') // Relasi
                    ->label('Direktorat Induk')
                    ->sortable()
                    ->searchable()
                    ->color('gray'),
                Tables\Columns\TextColumn::make('nama_unit')
                    ->label('Unit Kerja')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_unit')
                    ->label('Email Unit Kerja')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
                    ->label('Dihapus')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('id_direktorat')
                    ->relationship('direktorat', 'nama_direktorat')
                    ->label('Filter Direktorat'),
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListUnitKerjas::route('/'),
            'create' => Pages\CreateUnitKerja::route('/create'),
            'edit' => Pages\EditUnitKerja::route('/{record}/edit'),
        ];
    }
}
