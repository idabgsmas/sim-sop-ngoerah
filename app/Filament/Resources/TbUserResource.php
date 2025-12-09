<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TbUserResource\Pages;
use App\Filament\Resources\TbUserResource\RelationManagers;
use App\Models\TbUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class TbUserResource extends Resource
{
    protected static ?string $model = TbUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';

    protected static ?string $recordTitleAttribute = 'Pengguna';

    protected static ?string $navigationGroup = 'Data Pengguna';
    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Pengguna';

    protected static ?string $modelLabel = 'Pengguna';
    protected static ?string $pluralModelLabel = 'Pengguna';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Akun Pengguna')->schema([
                Forms\Components\TextInput::make('nama_lengkap')
                    ->label('Nama Lengkap')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('username')
                    ->label('Username')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(100),
                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(150),
                
                // Logic Password: Wajib saat Create, Opsional saat Edit
                Forms\Components\TextInput::make('password')
                    ->label('Password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state)) // Hash password
                    ->dehydrated(fn ($state) => filled($state)) // Hanya simpan jika diisi
                    ->required(fn (string $context): bool => $context === 'create'),
            ])->columns(2),

            Forms\Components\Section::make('Hak Akses & Penugasan')->schema([
                Forms\Components\Select::make('id_role')
                    ->relationship('role', 'nama_role')
                    ->required()
                    ->preload()
                    ->label('Role Akses'),

                Forms\Components\Select::make('id_direktorat')
                    ->relationship('direktorat', 'nama_direktorat')
                    ->label('Direktorat (Opsional)')
                    ->helperText('Diisi jika user mewakili direktorat tertentu')
                    ->searchable()
                    ->preload()
                    ->Live()
                    ->afterStateUpdated(function (Set $set) {
                        // 2. Jika Direktorat berubah, kosongkan pilihan Unit Kerja agar tidak nyangkut data lama
                        $set('unitKerja', []); 
                    }),
                
                // Relasi Many-to-Many ke Unit Kerja
                Forms\Components\Select::make('unitKerja') // Nama relasi di model TbUser
                    ->relationship(
                        name: 'unitKerja', 
                        titleAttribute: 'nama_unit',
                        modifyQueryUsing: function (Builder $query, Get $get) {
                            // 3. Ambil ID Direktorat yang sedang dipilih
                            $direktoratId = $get('id_direktorat');

                            // 4. Jika Direktorat belum dipilih, jangan tampilkan unit apapun (atau tampilkan semua, tergantung selera)
                            if (! $direktoratId) {
                                // Opsional: return $query->whereNull('id_unit_kerja'); // Kosongkan list
                                return $query; // Atau biarkan tampil semua
                            }

                            // 5. Filter Unit berdasarkan id_direktorat
                            return $query->where('id_direktorat', $direktoratId);
                        }
                    )
                    // ->multiple() // Bisa pilih banyak unit
                    ->preload()
                    ->searchable()
                    ->label('Penugasan Unit Kerja')
                    // Tambahkan kondisi: Field ini hanya aktif jika Direktorat sudah dipilih (Opsional, UX lebih baik)
                    ->disabled(fn (Get $get) => ! $get('id_direktorat')),
                    
                Forms\Components\Toggle::make('is_active')
                    ->label('Status Aktif')
                    ->default(true)
                    ->onColor('success')
                    ->offColor('danger'),
            ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nama_lengkap')
                    ->sortable()
                    ->searchable()
                    ->label('Nama Lengkap'),
                Tables\Columns\TextColumn::make('username')
                    ->sortable()
                    ->searchable()
                    ->label('Username'),
                Tables\Columns\TextColumn::make('role.nama_role')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Administrator' => 'danger',
                        'Verifikator' => 'warning',
                        'Viewer' => 'info',
                        'Direksi' => 'primary',
                        'Pengusul' => 'success',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('direktorat.nama_direktorat') // Menampilkan list unit kerja
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->label('Direktorat'),
                Tables\Columns\TextColumn::make('unitKerja.nama_unit') // Menampilkan list unit kerja
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(2)
                    ->label('Unit Kerja'),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label('Status Pengguna'),
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
                Tables\Filters\SelectFilter::make('id_role')
                    ->relationship('role', 'nama_role')
                    ->label('Filter Role'),
                Tables\Filters\SelectFilter::make('unitKerja.nama_unit')
                    ->relationship('unitKerja', 'nama_unit')
                    ->label('Filter Unit Kerja'),
                Tables\Filters\SelectFilter::make('direktorat.nama_direktorat')
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
            'index' => Pages\ListTbUsers::route('/'),
            'create' => Pages\CreateTbUser::route('/create'),
            'edit' => Pages\EditTbUser::route('/{record}/edit'),
        ];
    }
}
