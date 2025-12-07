<?php

namespace App\Filament\Pengusul\Resources\SopResource\Pages;

use App\Filament\Pengusul\Resources\SopResource;
use App\Models\Sop;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditSop extends EditRecord
{
    protected static string $resource = SopResource::class;

    // --- PERBAIKAN DI SINI ---
    protected function getFormActions(): array
    {
        return [
            // Gunakan getSaveFormAction(), bukan getSaveAction()
            $this->getSaveFormAction()
                ->label('Kirim Perbaikan & Ajukan Verifikasi'),
            
            $this->getSimpanDraftAction(),
            $this->getCancelFormAction(),
        ];
    }
    // -------------------------

    protected function getSimpanDraftAction(): Actions\Action
    {
        return Actions\Action::make('save_draft')
            ->label('Simpan Draft')
            ->color('gray')
            ->action(function () {
                $data = $this->form->getState();

                // Logic bongkar array file
                if (isset($data['dokumen_path'])) {
                    while (is_array($data['dokumen_path'])) {
                        if (empty($data['dokumen_path'])) {
                            $data['dokumen_path'] = null;
                            break;
                        }
                        $data['dokumen_path'] = reset($data['dokumen_path']);
                    }
                }
                
                if (isset($data['unitTerkait'])) {
                    unset($data['unitTerkait']);
                }
                
                $data['id_status'] = Sop::STATUS_DRAFT;
                
                // Gunakan logic update bawaan tapi dengan data yang sudah dimodifikasi
                $this->handleRecordUpdate($this->record, $data);
                $this->form->model($this->record)->saveRelationships();
                
                Notification::make()->title('Draft Disimpan')->success()->send();
                $this->redirect($this->getResource()::getUrl('index'));
            });
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        // Jika bukan draft, berarti user menekan tombol Kirim/Save utama
        // Maka lakukan validasi manual dan ubah status
        if (!isset($data['id_status']) || $data['id_status'] != Sop::STATUS_DRAFT) {
            
            // Validasi Manual (Karena di form schema kita buat nullable)
            $this->validate([
                'data.judul_sop' => 'required',
                'data.nomor_sop' => 'required',
                'data.kategori_sop' => 'required'   ,
                'data.dokumen_path' => 'required',
                'data.tgl_pengesahan' => 'required',
                'data.tgl_berlaku' => 'required',
            ]);
            
            $data['id_status'] = Sop::STATUS_BELUM_DIVERIFIKASI;
        }

        $record->update($data);
        return $record;
    }

    protected function afterSave(): void
    {
        $sop = $this->record;
        
        // Notif hanya jika status 'Belum Diverifikasi' (Bukan Draft)
        if ($sop->id_status == Sop::STATUS_BELUM_DIVERIFIKASI) {
            $verifikators = Sop::getVerifikators();
            Notification::make()
                ->title('Pengajuan SOP Baru')
                ->body("SOP '{$sop->judul_sop}' diajukan oleh unit " . auth()->user()->unitKerja->first()->nama_unit . " dan menunggu verifikasi.")
                // ->body("SOP '{$sop->judul_sop}' telah diperbarui dan menunggu verifikasi.")
                ->sendToDatabase($verifikators);
        }
    }
}