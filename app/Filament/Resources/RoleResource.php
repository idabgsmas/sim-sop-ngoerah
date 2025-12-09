<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RoleResource\Pages;
use App\Filament\Resources\RoleResource\RelationManagers;
use App\Models\Role;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RoleResource extends Resource
{
    protected static ?string $model = Role::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $recordTitleAttribute = 'Role';

    protected static ?string $navigationGroup = 'Data Pengguna';
    protected static ?int $navigationSort = 4;

    protected static ?string $navigationLabel = 'Role';
    protected static ?string $modelLabel = 'Role';
    protected static ?string $pluralModelLabel = 'Role';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nama_role')
                    ->required()
                    ->maxLength(50)
                    ->unique(ignoreRecord: true) // Pastikan nama role unik
                    ->label('Nama Role'),

                Forms\Components\Textarea::make('deskripsi_role')
                    ->label('Deskripsi Role')
                    ->maxLength(255)
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id_role')
                    ->searchable()
                    ->label('ID Role')
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_role')
                    ->searchable()
                    ->label('Nama Role')
                    ->sortable(),

                Tables\Columns\TextColumn::make('deskripsi_role')
                    ->label('Deskripsi Role')
                    ->limit(50)
                    ->sortable(),

            ])
            ->filters([
                //
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
            'index' => Pages\ListRoles::route('/'),
            'create' => Pages\CreateRole::route('/create'),
            'edit' => Pages\EditRole::route('/{record}/edit'),
        ];
    }
}
