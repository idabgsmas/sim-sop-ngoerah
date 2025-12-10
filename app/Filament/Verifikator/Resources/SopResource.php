<?php

namespace App\Filament\Verifikator\Resources;

use App\Filament\Verifikator\Resources\SopResource\Pages;
use App\Filament\Verifikator\Resources\SopResource\RelationManagers;
use App\Models\Sop;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action as InfolistAction;
use Filament\Forms\Components\Textarea;
use Joaopaulolndev\FilamentPdfViewer\Infolists\Components\PdfViewerEntry;

class SopResource extends Resource
{
    protected static ?string $model = Sop::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationLabel = 'Verifikasi Data SOP';
    protected static ?string $pluralModelLabel = 'Data SOP';

    public static function getEloquentQuery(): Builder
    {
        // Verifikator melihat SOP dengan status: Belum Diverifikasi, Dalam Revisi, Aktif, dan Kadaluarsa
        return parent::getEloquentQuery()->whereIn('id_status', [2,3,4,5]);
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
        // Filter Default: Verifikator idealnya melihat yang "Belum Diverifikasi" paling atas
        ->modifyQueryUsing(fn ($query) => $query->orderByRaw("FIELD(id_status, 2) DESC")->orderBy('created_at', 'desc'))
        ->columns([
            Tables\Columns\TextColumn::make('judul_sop')
                ->label('Judul SOP')
                ->sortable()
                ->limit(50)
                ->searchable(),
            Tables\Columns\TextColumn::make('unitKerja.nama_unit')
                ->label('Unit Pengusul')
                ->sortable()
                ->searchable(),
            Tables\Columns\TextColumn::make('kategori_sop')
                ->label('Kategori')
                ->sortable()
                ->searchable()
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'SOP Internal' => 'success',
                    'SOP AP' => 'info',
                }),
            Tables\Columns\TextColumn::make('tgl_berlaku')
                ->label('Tanggal Berlaku')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('tgl_kadaluwarsa')
                ->label('Tanggal Kadaluarsa')
                ->date()
                ->sortable(),
            Tables\Columns\TextColumn::make('status.nama_status')
                ->label('Status')
                ->sortable()
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
            Tables\Filters\SelectFilter::make('id_unit_kerja')
                    ->relationship('unitKerja', 'nama_unit')
                    ->label('Filter Unit Kerja'),
            Tables\Filters\SelectFilter::make('unitKerja.direktorat.nama_direktorat')
                    ->relationship('unitKerja.direktorat', 'nama_direktorat')
                    ->label('Filter Direktorat'),
            Tables\Filters\TrashedFilter::make(),
        ])

        ->actions([
            // Tombol Lihat Detail (View Only)
            Tables\Actions\ViewAction::make()
                ->label('')
                ->url(null), // <--- TAMBAHKAN BARIS INI (Set URL jadi null agar tidak pindah halaman)
                // ->tooltip('Lihat Detail (Modal)'), // Opsional: Tambah tooltip biar jelas

            // Tombol Download Dokumen
            Tables\Actions\Action::make('unduh')
                ->label('')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (Sop $record) => asset('storage/' . $record->dokumen_path))
                ->openUrlInNewTab(),

            // --- AKSI UTAMA VERIFIKATOR ---
            
            // 1. TOMBOL SETUJUI / VERIFIKASI
            Action::make('approve')
                ->label('Setujui')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Verifikasi SOP')
                ->modalDescription('Apakah Anda yakin SOP ini sudah sesuai dan siap diaktifkan?')
                ->visible(fn (Sop $record) => $record->id_status == Sop::STATUS_BELUM_DIVERIFIKASI) // Hanya muncul jika belum diverifikasi
                ->action(function (Sop $record) {
                    $record->update([
                        'id_status' => Sop::STATUS_AKTIF, // ID 4 = Aktif
                        // 'tgl_berlaku' => now(), // Opsional: Set tgl berlaku saat di-approve? (Tergantung kebijakan)
                        // Opsional: Catat siapa verifikatornya jika ada kolomnya
                        // 'verified_by' => auth()->id(),
                    ]);
                    
                    Notification::make()->title('SOP Berhasil Diverifikasi')
                        ->success()
                        ->send();
                    // (Nanti di sini kita selipkan notifikasi Service)
                }),

            // 2. TOMBOL TOLAK / MINTA REVISI
            Action::make('reject')
                ->label('Revisi')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('catatan_revisi')
                        ->label('Catatan Revisi')
                        ->required(),
                ])
                ->visible(fn (Sop $record) => $record->id_status == Sop::STATUS_BELUM_DIVERIFIKASI)
                ->action(function (Sop $record, array $data) {
                    $record->update([
                        'id_status' => Sop::STATUS_REVISI, // ID 3 = Revisi
                        // Anda bisa menyimpan catatan revisi ini di tabel history atau field khusus jika ada
                        // 'keterangan_revisi' => $data['catatan_revisi'] 
                    ]);
                    
                    // Kita bisa simpan history revisi di tabel tb_history_sop nanti
                    // Simpan catatan revisi (bisa ke history atau kolom khusus)
                    // Simpan ke history SOP
                    $record->histories()->create([
                        'id_user' => auth()->id(),
                        'id_status' => 3, // Status Revisi
                        'keterangan_perubahan' => $data['catatan_revisi'],
                        'dokumen_path' => $record->dokumen_path, // Snapshot file saat ini
                    ]);
                    
                    Notification::make()->title('SOP Dikembalikan untuk Revisi')->warning()->send();
                }),
        ]);
}

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // --- TAMBAHAN BARU: ALERT REVISI ---
                Infolists\Components\Section::make('Status: DALAM REVISI')
                    ->icon('heroicon-m-arrow-path')
                    // ->color('danger')
                    ->schema([
                        Infolists\Components\TextEntry::make('catatan_terakhir')
                            ->label('Catatan Revisi:')
                            ->state(function ($record) {
                                $history = $record->histories()
                                    ->where('id_status', 3)
                                    ->latest('created_at')
                                    ->first();
                                return $history ? $history->keterangan_perubahan : '-';
                            })
                            ->weight('bold')
                            ->color('danger')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                    ])
                    ->visible(fn ($record) => $record->id_status === 3), // Hanya muncul saat Revisi

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
            'view' => Pages\ViewSop::route('/{record}'),
        ];
    }
}
