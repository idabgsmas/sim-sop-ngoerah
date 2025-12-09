<?php

namespace App\Filament\Pengusul\Resources;

use App\Filament\Pengusul\Resources\SopResource\Pages;
use App\Filament\Pengusul\Resources\SopResource\RelationManagers;
use App\Models\Sop;
use App\Models\TbUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Get; // Penting untuk logika 'live'
use Filament\Forms\Set; // Penting untuk logika 'set' value
use Carbon\Carbon;      // Untuk hitung tanggal
use Illuminate\Support\Facades\Auth;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

class SopResource extends Resource
{
    protected static ?string $model = Sop::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Pengajuan SOP';
    protected static ?string $pluralModelLabel = 'Data SOP';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // --- BAGIAN 1: HEADER READ-ONLY (Info Pengusul) ---
                Forms\Components\Section::make('Informasi Pengusul')
                    ->schema([
                        Forms\Components\TextInput::make('nama_pengusul_display')
                            ->label('Nama Pengusul')
                            ->disabled()
                            ->dehydrated(false)
                            // LOGIKA BARU: Cek record
                            ->formatStateUsing(function ($record) {
                                // Jika sedang Edit ($record ada), ambil dari relasi uploader
                                if ($record) {
                                    return $record->uploader?->nama_lengkap;
                                }
                                // Jika sedang Create, ambil user login
                                return Auth::user()->nama_lengkap;
                            }),

                        Forms\Components\TextInput::make('unit_pengusul_display')
                            ->label('Unit Pengusul')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($record) {
                                if ($record) {
                                    return $record->unitKerja?->nama_unit;
                                }
                                return Auth::user()->unitKerja->first()?->nama_unit ?? '-';
                            }),

                        Forms\Components\TextInput::make('direktorat_display')
                            ->label('Direktorat')
                            ->disabled()
                            ->dehydrated(false)
                            ->formatStateUsing(function ($record) {
                                if ($record) {
                                    return $record->unitKerja?->direktorat?->nama_direktorat;
                                }
                                return Auth::user()->unitKerja->first()?->direktorat?->nama_direktorat ?? '-';
                            }),
                    ])->columns(3),

                // --- BAGIAN 2: INPUT SOP ---
                Forms\Components\Section::make('Detail Dokumen SOP')
                    ->schema([
                        Forms\Components\TextInput::make('judul_sop')
                            ->label('Judul SOP')
                            ->maxLength(255)
                            ->required(),
                            // ->columnSpan(2),

                        Forms\Components\TextInput::make('nomor_sop')
                            ->label('Nomor Dokumen SK SOP')
                            ->maxLength(100)
                            ->unique(ignoreRecord: true),

                        Forms\Components\Textarea::make('deskripsi')
                            ->label('Deskripsi Singkat')
                            ->columnSpanFull(),

                        // --- LOGIKA KATEGORI (Internal vs AP) ---
                        Forms\Components\Select::make('kategori_sop')
                            ->default('SOP Internal')
                            ->label('Kategori SOP')
                            ->options([
                                'SOP Internal' => 'SOP Internal (Hanya Unit Sendiri)',
                                'SOP AP' => 'SOP AP (Antar Profesi / Unit Lain)',
                            ])
                            ->live() // Live update untuk memunculkan form di bawahnya
                            ->afterStateUpdated(fn (Set $set) => $set('is_all_units', false))
                            ->columnSpanFull(), 

                        Forms\Components\Group::make()
                            ->schema([
                                Forms\Components\Toggle::make('is_all_units')
                                    ->label('Terkait dengan Semua Unit?')
                                    ->live()
                                    ->default(false),
                                
                                Forms\Components\Select::make('unitTerkait')
                                    ->relationship('unitTerkait', 'nama_unit')
                                    ->multiple()
                                    ->preload()
                                    ->searchable()
                                    ->label('Pilih Unit Terkait')
                                    ->hidden(fn (Get $get) => $get('is_all_units') === true) // Sembunyikan jika All Units
                                    ->columnSpanFull(),
                            ])
                            // Hanya muncul jika kategori SOP AP
                            ->visible(fn (Get $get) => $get('kategori_sop') === 'SOP AP')
                            ->columnSpanFull(),

                        Forms\Components\FileUpload::make('dokumen_path')
                            // ->nullable()
                            ->label('Dokumen SOP (PDF)')
                            ->acceptedFileTypes(['application/pdf'])
                            ->directory('sop-documents')
                            ->columnSpanFull()
                            ->disk('public'),
                    ])->columns(2),

                // --- BAGIAN 3: TANGGAL (Auto Calculate) ---
                Forms\Components\Section::make('Masa Berlaku & Jadwal')
                    ->schema([
                        Forms\Components\DatePicker::make('tgl_pengesahan')
                            ->icon('heroicon-o-calendar')
                            ->label('Tanggal Pengesahan (Tanda Tangan)')
                            ->native(false),

                        Forms\Components\DatePicker::make('tgl_berlaku')
                            ->icon('heroicon-o-calendar')
                            ->label('Tanggal Berlaku (TMT)')
                            ->live() // Trigger perhitungan
                            ->native(false)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (!$state) return;
                                
                                // LOGIKA: Hitung Expired & Review berdasarkan Tgl Berlaku
                                $tmt = Carbon::parse($state);
                                
                                // Expired = +3 Tahun
                                $set('tgl_kadaluwarsa', $tmt->copy()->addYears(3)->format('Y-m-d'));
                                
                                // Review = +2 Tahun (1 tahun sebelum expired)
                                $set('tgl_review_tahunan', $tmt->copy()->addYears(1)->format('Y-m-d'));
                            }),

                        Forms\Components\DatePicker::make('tgl_review_tahunan')
                            ->icon('heroicon-o-calendar')
                            ->label('Jadwal Review Tahunan')
                            ->readOnly() // Otomatis
                            ->helperText('Otomatis setiap tahun dari TMT'),

                        Forms\Components\DatePicker::make('tgl_kadaluwarsa')
                            ->icon('heroicon-o-calendar')
                            ->label('Tanggal Kadaluwarsa')
                            ->readOnly() // Otomatis
                            ->helperText('Otomatis 3 tahun dari TMT'),
                        
                    ])->columns(2),
                
                // HIDDEN FIELDS
                Forms\Components\Hidden::make('id_user')
                    ->default(fn () => Auth::id()),
                Forms\Components\Hidden::make('id_unit_kerja')
                    ->default(fn () => Auth::user()->unitKerja->first()?->id_unit_kerja),
                Forms\Components\Hidden::make('id_status'), // Diatur lewat tombol
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('judul_sop')
                    ->limit(30)
                    ->searchable()
                    ->sortable()
                    ->label('Judul SOP'),
                // Tables\Columns\TextColumn::make('nomor_sop')->label('No. Dokumen'),
                Tables\Columns\TextColumn::make('kategori_sop')
                    ->badge()
                    ->label('Kategori SOP')
                    ->sortable()
                    ->color(fn (string $state): string => match ($state) {
                    'SOP Internal' => 'success',
                    'SOP AP' => 'info',
                }),
                Tables\Columns\TextColumn::make('tgl_berlaku')
                    ->date()
                    ->sortable()
                    ->label('Tanggal Berlaku'),
                Tables\Columns\TextColumn::make('tgl_kadaluwarsa')
                    ->date()
                    ->sortable()
                    ->label('Tanggal Kadaluwarsa'),
                Tables\Columns\TextColumn::make('status.nama_status')
                    ->label('Status')
                    ->sortable()
                    ->searchable()
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Draft' => 'gray',
                        'Belum Diverifikasi' => 'warning',
                        'Dalam Revisi' => 'danger',
                        default => 'info',
                    }),
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
                Tables\Filters\SelectFilter::make('kategori_sop')
                    ->options([
                        'SOP Internal' => 'SOP Internal',
                        'SOP AP' => 'SOP AP',
                    ])
                    //   ->relationship('kategoriSop', 'nama_kategori_sop')
                    ->label('Filter Kategori SOP'),
                Tables\Filters\SelectFilter::make('id_status')
                    ->relationship('status', 'nama_status')
                    ->label('Filter Status SOP'),
                Tables\Filters\TrashedFilter::make(),
        ])
        
            ->actions([
                Tables\Actions\ViewAction::make(), // Tombol Lihat Detail (View Only)
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // --- HEADER INFORMASI ---
                Infolists\Components\Section::make('Informasi Dokumen')
                    ->schema([
                        Infolists\Components\TextEntry::make('judul_sop')
                            ->label('Judul SOP')
                            ->weight('bold')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large)
                            ->columnSpanFull(),
                        
                        Infolists\Components\TextEntry::make('nomor_sop')
                            ->label('Nomor Dokumen')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('deskripsi')
                            ->label('Deskripsi Singkat')
                            ->html()
                            // ->columnSpanFull()
                            ->placeholder('Tidak ada deskripsi'),

                        // MENAMPILKAN UNIT & DIREKTORAT
                        Infolists\Components\TextEntry::make('unitKerja.nama_unit')
                                ->label('Unit Pengusul')
                                ->icon('heroicon-m-building-office'),
                                // ->weight('bold'),
                                
                        Infolists\Components\TextEntry::make('unitKerja.direktorat.nama_direktorat')
                                ->label('Direktorat')
                                ->icon('heroicon-m-building-library')
                                ->color('gray'),
                        
                        Infolists\Components\TextEntry::make('kategori_sop')
                            ->badge()
                            ->color('info'),
                            
                        Infolists\Components\TextEntry::make('status.nama_status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Aktif' => 'success',
                                'Draft' => 'gray',
                                'Belum Diverifikasi' => 'warning',
                                'Dalam Revisi' => 'danger',
                                default => 'info',
                            }),
                    ])->columns(2),

                // --- TANGGAL PENTING ---
                Infolists\Components\Section::make('Detail Tanggal')
                    ->schema([
                        Infolists\Components\TextEntry::make('tgl_pengesahan')
                            ->label('Tanggal Disahkan')
                            ->date(),
                        
                        Infolists\Components\TextEntry::make('tgl_berlaku')
                            ->label('Tanggal Berlaku (TMT)')
                            ->date(),
                        
                        Infolists\Components\TextEntry::make('tgl_kadaluwarsa')
                            ->label('Berlaku Sampai')
                            ->date()
                            ->color('danger'),
                            
                        Infolists\Components\TextEntry::make('tgl_review_tahunan')
                            ->label('Jadwal Review')
                            ->date(),
                    ])->columns(4),
                
                // --- TAMBAHAN BARU: SECTION UNIT TERKAIT (SOP AP) ---
                Infolists\Components\Section::make('Keterkaitan Unit (SOP AP)')
                    // ->icon('heroicon-m-link')
                    // LOGIC 1: Hanya tampil jika Kategori = SOP AP
                    ->visible(fn (Sop $record) => $record->kategori_sop === 'SOP AP') 
                    ->schema([
                        // Tampilkan status apakah Semua Unit atau Unit Spesifik
                        Infolists\Components\TextEntry::make('is_all_units')
                            ->label('Cakupan Keterkaitan')
                            ->formatStateUsing(fn (bool $state) => $state ? 'Seluruh Unit Kerja' : 'Unit Kerja Spesifik')
                            ->badge()
                            ->color(fn (bool $state) => $state ? 'danger' : 'primary')
                            ->icon(fn (bool $state) => $state ? 'heroicon-m-globe-alt' : 'heroicon-m-users'),

                        // LOGIC 2: List Unit (Hanya muncul jika BUKAN Semua Unit)
                        Infolists\Components\TextEntry::make('unitTerkait.nama_unit')
                            ->label('Daftar Unit Terkait')
                            ->listWithLineBreaks()
                            ->bulleted() 
                            ->visible(fn (Sop $record) => ! $record->is_all_units) // Sembunyikan jika is_all_units = true
                            // ->columnSpanFull()
                            ->placeholder('Belum ada unit terkait yang dipilih'),
                    ])->columns(2),
                // -----------------------------------------------------

                // --- ISI & PREVIEW DOKUMEN ---
                Infolists\Components\Section::make('Isi & Lampiran')
                    ->schema([
                        // --- PDF VIEWER (IFRAME) ---
                        PdfViewerEntry::make('dokumen_path')
                            ->label('Pratinjau Dokumen')
                            ->minHeight('80svh') // Tinggi viewer (80% layar)
                            ->fileUrl(fn ($record) => asset('storage/' . $record->dokumen_path))
                            ->columnSpanFull(),
                    ]),
            ]);
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