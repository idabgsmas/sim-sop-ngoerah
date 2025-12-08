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
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

class SopResource extends Resource
{
    protected static ?string $model = Sop::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'Daftar Data SOP';
    protected static ?string $pluralModelLabel = 'Data SOP';

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
            Tables\Columns\TextColumn::make('judul_sop')
                ->label('Judul SOP')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('direktorat.nama_direktorat')
                ->label('Direktorat')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('unitKerja.nama_unit')
                ->label('Unit')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('tgl_berlaku')
                ->date()
                ->label('Tanggal Berlaku')
                ->sortable(),
            Tables\Columns\TextColumn::make('tgl_kadaluwarsa')
                ->date()
                ->label('Tanggal Kadaluarsa')
                ->sortable(),
        ])
        ->actions([
            // Viewer hanya bisa View & Download
            Tables\Actions\ViewAction::make(),
            Tables\Actions\Action::make('unduh')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (Sop $record) => asset('storage/' . $record->dokumen_path))
                ->openUrlInNewTab(),
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
