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
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

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
                ->badge()
                ->color(fn (string $state): string => match ($state) {
                    'Aktif' => 'success',
                    'Draft' => 'gray',
                    'Belum Diverifikasi' => 'warning',
                    'Dalam Revisi' => 'danger',
                    default => 'info',
                }),
            
        ])

        ->filters([
            //
        ])

        ->actions([
            // Tombol Lihat Detail (View Only)
            Tables\Actions\ViewAction::make(),

            // Tombol Download Dokumen
            Tables\Actions\Action::make('download')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(fn (Sop $record) => asset('storage/' . $record->dokumen_path))
                ->openUrlInNewTab(),

            // --- AKSI UTAMA VERIFIKATOR ---
            
            // 1. TOMBOL SETUJUI / VERIFIKASI
            Action::make('approve')
                ->label('Verifikasi')
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
                    ]);
                    
                    Notification::make()->title('SOP Berhasil Diverifikasi')
                        ->success()
                        ->send();
                }),

            // 2. TOMBOL TOLAK / MINTA REVISI
            Action::make('reject')
                ->label('Revisi')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->form([
                    \Filament\Forms\Components\Textarea::make('catatan_revisi')
                        ->label('Catatan Revisi untuk Pengusul')
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
                    
                    Notification::make()->title('SOP Dikembalikan untuk Revisi')->warning()->send();
                }),
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
