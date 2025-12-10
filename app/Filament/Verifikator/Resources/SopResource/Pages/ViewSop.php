<?php

namespace App\Filament\Verifikator\Resources\SopResource\Pages;

use App\Filament\Verifikator\Resources\SopResource;
use App\Models\Sop;
use Filament\Actions; // Gunakan Actions Page, bukan Infolist Action
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;

class ViewSop extends ViewRecord
{
    protected static string $resource = SopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // 1. TOMBOL SETUJUI
            Actions\Action::make('approve')
                ->label('Setujui Dokumen')
                ->icon('heroicon-m-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Verifikasi SOP')
                ->modalDescription('Apakah Anda yakin SOP ini sudah sesuai?')
                ->visible(fn (Sop $record) => $record->id_status == Sop::STATUS_BELUM_DIVERIFIKASI)
                ->action(function (Sop $record) {
                    $record->update(['id_status' => Sop::STATUS_AKTIF]);
                    
                    Notification::make()->title('SOP Disetujui')->success()->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),

            // 2. TOMBOL REVISI
            Actions\Action::make('reject')
                ->label('Minta Revisi')
                ->icon('heroicon-m-x-circle')
                ->color('danger')
                ->form([
                    Textarea::make('catatan_revisi')
                        ->label('Catatan Revisi')
                        ->required()
                        ->rows(4),
                ])
                ->visible(fn (Sop $record) => $record->id_status == Sop::STATUS_BELUM_DIVERIFIKASI)
                ->action(function (Sop $record, array $data) {
                    $record->histories()->create([
                        'id_user' => auth()->id(),
                        'id_status' => Sop::STATUS_REVISI,
                        'keterangan_perubahan' => $data['catatan_revisi'],
                        'dokumen_path' => $record->dokumen_path,
                    ]);

                    $record->update(['id_status' => Sop::STATUS_REVISI]);
                    
                    Notification::make()->title('SOP Dikembalikan')->warning()->send();
                    $this->redirect($this->getResource()::getUrl('index'));
                }),
        ];
    }
}