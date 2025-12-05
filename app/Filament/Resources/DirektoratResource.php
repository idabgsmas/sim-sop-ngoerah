<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DirektoratResource\Pages;
use App\Filament\Resources\DirektoratResource\RelationManagers;
use App\Models\Direktorat;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DirektoratResource extends Resource
{
    protected static ?string $model = Direktorat::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $recordTitleAttribute = 'Direktorat';

    protected static ?string $navigationGroup = 'Master Data';
    protected static ?int $navigationSort = 1;

    protected static ?string $navigationLabel = 'Direktorat';

    protected static ?string $modelLabel = 'Direktorat';
    protected static ?string $pluralModelLabel = 'Direktorat';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Direktorat')->schema([
                Forms\Components\TextInput::make('kode_direktorat')
                    ->label('Kode Direktorat')
                    ->required()
                    ->maxLength(5)
                    ->unique(ignoreRecord: true),
                Forms\Components\TextInput::make('nama_direktorat')
                    ->label('Nama Direktorat')
                    ->required()
                    ->maxLength(100),
                Forms\Components\TextInput::make('email_direktorat')
                    ->label('Email Direktorat')
                    ->email()
                    ->maxLength(100),
                Forms\Components\TextInput::make('no_telp')
                    ->label('No. Telepon Direktorat')
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
                Tables\Columns\TextColumn::make('kode_direktorat')
                    ->label('Kode Direktorat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nama_direktorat')
                    ->label('Direktorat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_direktorat')
                    ->label('Email Direktorat')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_telp')
                    ->label('No. Telepon Direktorat')
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
            ])
            ->filters([
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
            'index' => Pages\ListDirektorats::route('/'),
            'create' => Pages\CreateDirektorat::route('/create'),
            'edit' => Pages\EditDirektorat::route('/{record}/edit'),
        ];
    }
}
