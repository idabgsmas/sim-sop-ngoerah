<?php

namespace App\Filament\Pengusul\Widgets;

use App\Models\Sop;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class LatestReviews extends BaseWidget
{
    // Atur urutan tampilan (sesuaikan dengan Dashboard.php nanti)
    protected static ?int $sort = 3; 
    
    // Agar widget melebar penuh
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $heading = 'Perlu Segera Review Tahunan';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Sop::query()
                    // 1. Filter Unit Kerja Saya
                    ->whereIn('id_unit_kerja', Auth::user()->unitKerja->pluck('id_unit_kerja')->toArray())
                    // 2. Status Harus Aktif (4)
                    ->where('id_status', Sop::STATUS_AKTIF) 
                    // 3. Tanggal Review ada di rentang HARI INI s.d 30 HARI LAGI
                    ->whereBetween('tgl_review_tahunan', [now()->startOfDay(), now()->addDays(30)->endOfDay()])
                    // 4. Urutkan dari yang paling mepet (deadline terdekat)
                    ->orderBy('tgl_review_tahunan', 'asc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('judul_sop')
                    ->label('Judul SOP')
                    ->searchable()
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->judul_sop),

                Tables\Columns\TextColumn::make('tgl_review_tahunan')
                    ->label('Jadwal Review')
                    ->date('d M Y')
                    ->sortable()
                    ->icon('heroicon-m-calendar'),

                // Kolom Sisa Hari (Countdown)
                Tables\Columns\TextColumn::make('sisa_hari')
                    ->label('Tenggat Waktu')
                    ->state(function (Sop $record) {
                        $days = now()->startOfDay()->diffInDays($record->tgl_review_tahunan, false);
                        if ($days == 0) return 'HARI INI';
                        return $days . ' Hari Lagi';
                    })
                    ->badge()
                    ->color(fn ($state) => $state === 'HARI INI' ? 'danger' : 'warning'),
            ])
            ->actions([
                // --- COPY LOGIC ACTION GROUP DARI SOP RESOURCE ---
                Tables\Actions\ActionGroup::make([
                    
                    // Aksi 1: Tidak Ada Perubahan
                    Tables\Actions\Action::make('confirm_review')
                        ->label('Tanpa Perubahan')
                        ->icon('heroicon-m-check-badge')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalDescription('Konfirmasi bahwa SOP ini masih relevan? Jadwal review akan diperbarui ke tahun depan.')
                        ->action(function (Sop $record) {
                            $nextReview = Carbon::parse($record->tgl_review_tahunan)->addYear();
                            
                            if ($record->tgl_kadaluwarsa && $nextReview->gte($record->tgl_kadaluwarsa)) {
                                 $record->update(['tgl_review_tahunan' => null]);
                                 Notification::make()->title('Review Terakhir Selesai')->body('Tahun depan expired.')->warning()->send();
                            } else {
                                $record->update(['tgl_review_tahunan' => $nextReview]);
                                Notification::make()->title('Review Tahunan Berhasil')->success()->send();
                            }
                        }),

                    // Aksi 2: Ada Perubahan
                    Tables\Actions\Action::make('change_review')
                        ->label('Lakukan Perubahan')
                        ->icon('heroicon-m-pencil-square')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->modalDescription('Status akan berubah menjadi REVISI agar Anda bisa mengupload file baru. Lanjutkan?')
                        ->action(function (Sop $record) {
                            $record->update(['id_status' => Sop::STATUS_REVISI]);
                            return redirect()->route('filament.pengusul.resources.sops.edit', $record);
                        }),

                ])
                ->label('Aksi Review')
                ->icon('heroicon-m-clock')
                ->color('danger')
                ->button() // Agar tampil sebagai tombol langsung
            ]);
    }
}