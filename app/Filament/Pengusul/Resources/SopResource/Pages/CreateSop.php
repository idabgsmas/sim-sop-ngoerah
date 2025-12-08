<?php

namespace App\Filament\Pengusul\Resources\SopResource\Pages;

use App\Filament\Pengusul\Resources\SopResource;
use App\Models\Sop;
use App\Models\Notifikasi;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action as NotifAction;
use Filament\Resources\Pages\CreateRecord;

class CreateSop extends CreateRecord
{
    protected static string $resource = SopResource::class;

    protected function getFormActions(): array
    {
        return [
            $this->getKirimAction(),
            $this->getSimpanDraftAction(),
            $this->getCancelFormAction(),
        ];
    }

    // Tombol Kirim (Hanya Submit, logic di mutateFormDataBeforeCreate)
    protected function getKirimAction(): Actions\Action
    {
        return Actions\Action::make('create')
            ->label('Kirim & Ajukan Verifikasi')
            ->submit('create') 
            ->keyBindings(['mod+s'])
            ->color('primary');
    }

    // Validasi & Status sebelum Create
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->validate([
            'data.judul_sop'        => 'required',
            'data.nomor_sop'        => 'required',
            'data.kategori_sop'     => 'required',
            'data.dokumen_path'     => 'required',
            'data.tgl_pengesahan'   => 'required', 
            'data.tgl_berlaku'      => 'required',
            'data.tgl_review_tahunan' => 'required',
            'data.deskripsi'        => 'required',
        ]);

        $data['id_status'] = Sop::STATUS_BELUM_DIVERIFIKASI;
        
        return $data;

        $this->redirect($this->getResource()::getUrl('index'));
    }

    // Tombol Draft
    protected function getSimpanDraftAction(): Actions\Action
    {
        return Actions\Action::make('save_draft')
            ->label('Simpan sebagai Draft')
            ->color('gray')
            ->action(function () {
                $data = $this->form->getState();
                
                if (isset($data['dokumen_path'])) {
                    while (is_array($data['dokumen_path'])) {
                        if (empty($data['dokumen_path'])) {
                            $data['dokumen_path'] = null;
                            break;
                        }
                        $data['dokumen_path'] = reset($data['dokumen_path']);
                    }
                }

                if (isset($data['unitTerkait'])) unset($data['unitTerkait']);
                
                $data['id_status'] = Sop::STATUS_DRAFT; 

                // Bypass dengan handleRecordCreation manual
                $this->record = $this->handleRecordCreation($data);
                $this->form->model($this->record)->saveRelationships();
                
                Notification::make()->title('Draft SOP Disimpan')->success()->send();
                $this->redirect($this->getResource()::getUrl('index'));
            });
    }

    // // Notifikasi
    // protected function afterCreate(): void
    // {
    //     $sop = $this->record;
    //     if ($sop->id_status == Sop::STATUS_BELUM_DIVERIFIKASI) {
    //         $verifikators = Sop::getVerifikators();
            
    //         foreach ($verifikators as $verifikator) {
    //             // DB Custom
    //             Notifikasi::create([
    //                 'id_user'   => $verifikator->id_user,
    //                 'id_sop'    => $sop->id_sop,
    //                 'judul'     => 'Pengajuan SOP Baru',
    //                 'isi_notif' => "SOP '{$sop->judul_sop}' diajukan oleh unit " . auth()->user()->unitKerja->first()->nama_unit,
    //                 'is_read'   => false,
    //                 'created_by'=> auth()->user()->nama_lengkap,
    //             ]);

    //             // Lonceng
    //             Notification::make()
    //                 ->title('Pengajuan SOP Baru')
    //                 ->body("SOP '{$sop->judul_sop}' menunggu verifikasi.")
    //                 ->icon('heroicon-o-document-plus') 
    //                 ->actions([
    //                     NotifAction::make('view')
    //                         ->url('/admin/sops/' . $sop->id_sop . '/edit')
    //                         ->label('Lihat SOP')
    //                         ->markAsRead(),
    //                 ])
    //                 ->sendToDatabase($verifikator);
    //         }
    //     }
    // }
}