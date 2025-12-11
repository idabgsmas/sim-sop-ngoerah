<?php

namespace App\Filament\Pengusul\Resources;

use App\Models\Sop;
use Filament\Forms;
use Filament\Tables;
use App\Models\TbUser;
use Filament\Infolists;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;      // Untuk hitung tanggal
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Pengusul\Resources\SopResource\Pages;
use Filament\Forms\Get; // Penting untuk logika 'live'
use Filament\Forms\Set; // Penting untuk logika 'set' value
use App\Filament\Pengusul\Resources\SopResource\RelationManagers;
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
                // --- TAMBAHAN BARU: SECTION CATATAN REVISI ---
                Forms\Components\Section::make('PERHATIAN: DOKUMEN PERLU DIPERBAIKI')
                    ->icon('heroicon-m-exclamation-triangle')
                    // ->color('danger') // Warna Merah
                    ->schema([
                        Forms\Components\Placeholder::make('catatan_revisi_display')
                            ->label('Catatan dari Verifikator:')
                            ->content(function ($record) {
                                // Ambil history terakhir yang statusnya 'Revisi' (ID 3)
                                $history = $record->histories()
                                    ->where('id_status', 3) 
                                    ->latest('created_at')
                                    ->first();
                                
                                return $history ? $history->keterangan_perubahan : 'Tidak ada catatan spesifik.';
                            })
                            ->extraAttributes([
                                'class' => 'bg-red-50 text-red-700 p-4 rounded-lg border border-red-200 font-medium',
                            ]),
                            
                    ])
                    // Logic: Hanya muncul jika Status = Revisi (ID 3)
                    ->visible(fn ($record) => $record && $record->id_status === 3),

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
                            ->required()
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
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                // 1. Reset toggle 'Semua Unit' jika kategori berubah
                                $set('is_all_units', false);

                                // 2. LOGIKA OTOMATIS PILIH UNIT PENGUSUL
                                // Jika user memilih 'SOP AP', otomatis masukkan unit user login ke field 'unitTerkait'
                                if ($state === 'SOP AP') {
                                    // Ambil data yang sudah terpilih saat ini (jika ada)
                                    $currentUnits = $get('unitTerkait') ?? [];
                                    
                                    // Ambil ID Unit Kerja milik User yang sedang login
                                    $myUnitIds = Auth::user()->unitKerja->pluck('id_unit_kerja')->toArray();
                                    
                                    // Gabungkan unit yang sudah ada dengan unit saya (agar tidak menimpa data lama jika diedit)
                                    // array_unique memastikan tidak ada ID ganda
                                    $mergedUnits = array_unique(array_merge($currentUnits, $myUnitIds));
                                    
                                    // Set nilai ke field unitTerkait
                                    $set('unitTerkait', $mergedUnits);
                                }
                            })
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
                                    // Otomatis pilih unit kerja milik user yang sedang login saat form dibuka (hanya saat Create)
                                    ->default(function () {
                                        // Ambil ID semua unit kerja milik user login
                                        return Auth::user()->unitKerja->pluck('id_unit_kerja')->toArray();
                                    })
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
                // Tombol View (Bisa dilihat oleh semua yang muncul di list)
                Tables\Actions\ViewAction::make(),

                // Tombol Edit (Hanya muncul jika SOP ini MILIK Unit User)
                // --- MODIFIKASI TOMBOL EDIT/REVISI ---
                Tables\Actions\EditAction::make()
                    // 1. Ganti Label: Jika status Revisi (3), jadi "Lakukan Revisi"
                    ->label(fn (Sop $record) => $record->id_status === 3 ? 'Perbaiki' : 'Ubah')
                    
                    // 2. Ganti Warna: Kuning/Orange biar mencolok saat Revisi
                    ->color(fn (Sop $record) => $record->id_status === 3 ? 'warning' : 'primary')
                    
                    // 3. Ganti Ikon: Obeng/Wrench saat Revisi (Opsional)
                    ->icon(fn (Sop $record) => $record->id_status === 3 ? 'heroicon-m-pencil-square' : 'heroicon-m-pencil-square')
                    
                    // 4. Logic Visibility (Tetap sama seperti sebelumnya: Hanya unit sendiri)
                    ->visible(function (Sop $record) {
                        $user = Auth::user();
                        $myUnitIds = $user->unitKerja->pluck('id_unit_kerja')->toArray();
                        return in_array($record->id_unit_kerja, $myUnitIds);
                    }),
                
                // --- TOMBOL KHUSUS: REVIEW TAHUNAN ---
                Tables\Actions\ActionGroup::make([
                // AKSI 1: KONFIRMASI (TIDAK ADA PERUBAHAN)
                    Tables\Actions\Action::make('confirm_review')
                        ->label('Review: Tidak Ada Perubahan')
                        ->icon('heroicon-m-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Konfirmasi bahwa SOP ini masih relevan? Jadwal review akan diperbarui ke tahun depan.')
                        ->action(function (Sop $record) {
                            $nextReview = Carbon::parse($record->tgl_review_tahunan)->addYear();
                            
                            // Cek Expired
                            if ($record->tgl_kadaluwarsa && $nextReview->gte($record->tgl_kadaluwarsa)) {
                                // Stop Review, fokus expired
                                $record->update(['tgl_review_tahunan' => null]);
                                
                                Notification::make()
                                    ->title('Review Terakhir Selesai')
                                    ->body('Tahun depan SOP sudah expired. Jadwal review dihentikan.')
                                    ->warning()
                                    ->send();
                            } else {
                                // Update ke tahun depan
                                $record->update(['tgl_review_tahunan' => $nextReview]);
                                
                                Notification::make()
                                    ->title('Review Tahunan Berhasil')
                                    ->body('Jadwal review diperbarui ke tahun depan.')
                                    ->success()
                                    ->send();
                            }
                        }),

                    // AKSI 2: LAKUKAN PERUBAHAN (EDIT)
                    Tables\Actions\Action::make('change_review')
                        ->label('Review: Lakukan Perubahan')
                        ->icon('heroicon-m-pencil-square')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalDescription('Status akan berubah menjadi REVISI agar Anda bisa mengupload file baru. Lanjutkan?')
                        ->action(function (Sop $record) {
                            // 1. Update Tanggal Review DULU (karena user sudah merespon review ini)
                            // Kita asumsikan jika mereka merevisi sekarang, review tahunan "dianggap done" untuk siklus ini.
                            // Nanti saat diajukan ulang & aktif, tanggal review akan dihitung ulang otomatis 
                            // oleh logic 'afterStateUpdated' di Form Create/Edit jika TMT berubah.
                            // TAPI, jika TMT tidak berubah, kita perlu manual bump tanggalnya di sini atau saat approve.
                            
                            // Strategi Aman: Kita update status jadi REVISI.
                            // Masalah tanggal review tahun depan biarkan ditangani saat Approval Verifikator nanti (atau form edit).
                            
                            $record->update([
                                'id_status' => Sop::STATUS_REVISI, 
                            ]);

                            // Opsional: Catat history
                            $record->histories()->create([
                                'id_user' => auth()->id(),
                                'id_status' => Sop::STATUS_REVISI,
                                'keterangan_perubahan' => 'Review Tahunan: Melakukan perubahan dokumen.',
                                'dokumen_path' => $record->dokumen_path,
                            ]);
                            
                            return redirect()->route('filament.pengusul.resources.sops.edit', $record);
                        }),

                ])
                ->label('Aksi Review')
                ->icon('heroicon-m-clock')
                ->color('danger') // Merah biar urgency tinggi
                // LOGIC VISIBILITY: Hanya muncul H-30 s.d Hari H
                ->visible(function (Sop $record) {
                    if (!$record->tgl_review_tahunan || $record->id_status !== 4) return false;
                    
                    $today = now()->startOfDay(); // Pastikan jam 00:00 agar akurat
                    $reviewDate = Carbon::parse($record->tgl_review_tahunan)->startOfDay();
                    $startDate = $reviewDate->copy()->subDays(30);
                    
                    // Cek rentang waktu (30 hari sebelum S.D. Hari H)
                    return $today->between($startDate, $reviewDate->copy());
                }),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // --- 1. TAMBAHAN BARU: ALERT REVISI (Agar muncul di Modal View) ---
                Infolists\Components\Section::make('PERHATIAN: DOKUMEN PERLU DIPERBAIKI')
                    ->icon('heroicon-m-exclamation-triangle')
                    // ->color('danger') 
                    ->schema([
                        Infolists\Components\TextEntry::make('catatan_revisi_display')
                            ->label('Catatan dari Verifikator:')
                            ->state(function ($record) {
                                $history = $record->histories()
                                    ->where('id_status', 3) // ID 3 = Revisi
                                    ->latest('created_at')
                                    ->first();
                                return $history ? $history->keterangan_perubahan : 'Tidak ada catatan spesifik.';
                            })
                            ->weight('bold')
                            ->color('danger')
                            ->size(Infolists\Components\TextEntry\TextEntrySize::Large),
                    ])
                    // Hanya muncul jika status = Revisi (ID 3)
                    ->visible(fn ($record) => $record && $record->id_status === 3),


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

    public static function getEloquentQuery(): Builder
    {
        $user = Auth::user();
        
        // 1. Ambil ID Unit Kerja user yang sedang login (bisa lebih dari satu)
        // Pastikan relasi 'unitKerja' di model TbUser sudah benar (belongsToMany)
        $myUnitIds = $user->unitKerja->pluck('id_unit_kerja')->toArray();

        return parent::getEloquentQuery()
            ->where(function ($query) use ($myUnitIds) {
                // SKENARIO A: Tampilkan SOP milik Unit Saya (Apapun statusnya: Draft, Revisi, Aktif, dll)
                $query->whereIn('id_unit_kerja', $myUnitIds)
                
                // SKENARIO B: Tampilkan SOP Unit Lain (SOP AP)
                ->orWhere(function ($q) use ($myUnitIds) {
                    $q->where('kategori_sop', 'SOP AP') // Wajib SOP AP
                    ->where('id_status', 4)           // Wajib Status AKTIF (ID 4)
                    ->where(function ($sub) use ($myUnitIds) {
                        // Sub-kondisi: Apakah terkait dengan unit saya?
                        $sub->where('is_all_units', true) // Jika dicentang "Semua Unit"
                            ->orWhereHas('unitTerkait', function ($rel) use ($myUnitIds) {
                                // Atau ID unit saya ada di tabel pivot unitTerkait
                                $rel->whereIn('tb_unit_kerja.id_unit_kerja', $myUnitIds);
                            });
                    });
                });
            });
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