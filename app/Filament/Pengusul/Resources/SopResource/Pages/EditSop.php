<?php

namespace App\Filament\Pengusul\Resources\SopResource\Pages;

use App\Models\Sop;
use Filament\Actions;
use Filament\Pages\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Pengusul\Resources\SopResource;
use Filament\Notifications\Actions\Action as NotifAction;
use App\Models\Notifikasi; // Pastikan model notifikasi diimport

class EditSop extends EditRecord
{
    protected static string $resource = SopResource::class;

    protected function getFormActions(): array
    {
        return [
            // Tombol Kirim Standar (Memicu mutateFormDataBeforeSave)
            $this->getSaveFormAction()
                ->label('Kirim Perbaikan & Ajukan Verifikasi'),
            
            // Tombol Draft Custom
            $this->getSimpanDraftAction(),
            $this->getCancelFormAction(),
        ];
    }

    // --- 1. LOGIKA TOMBOL KIRIM (STANDARD SAVE) ---
    // Metode ini otomatis jalan saat tombol "Kirim Perbaikan" ditekan
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Validasi Manual: Pastikan semua data lengkap sebelum dikirim
        $this->validate([
            'data.judul_sop'        => 'required',
            'data.nomor_sop'        => 'required',
            'data.kategori_sop'     => 'required',
            'data.dokumen_path'     => 'required',
            
            // Sesuai nama kolom di database Anda
            'data.tgl_pengesahan'   => 'required', 
            'data.tgl_berlaku'      => 'required',
            'data.tgl_review_tahunan' => 'required',
            'data.deskripsi'        => 'required', 
        ]);

        // PAKSA ubah status jadi "Belum Diverifikasi" (ID 2)
        // Tidak peduli status sebelumnya apa, kalau dikirim, berarti minta verifikasi
        $data['id_status'] = Sop::STATUS_BELUM_DIVERIFIKASI;

        // Redirect ke index
        $this->redirect($this->getResource()::getUrl('index'));

        return $data;
    }

    // --- 2. LOGIKA TOMBOL DRAFT (CUSTOM) ---
    protected function getSimpanDraftAction(): Actions\Action
    {
        return Actions\Action::make('save_draft')
            ->label('Simpan Draft')
            ->color('gray')
            ->action(function () {
                $data = $this->form->getState();

                // Logic bongkar array file jika Filament membungkusnya
                if (isset($data['dokumen_path'])) {
                    while (is_array($data['dokumen_path'])) {
                        if (empty($data['dokumen_path'])) {
                            $data['dokumen_path'] = null;
                            break;
                        }
                        $data['dokumen_path'] = reset($data['dokumen_path']);
                    }
                }
                
                // Bersihkan relasi many-to-many dari data insert utama
                if (isset($data['unitTerkait'])) {
                    unset($data['unitTerkait']);
                }
                
                // Set Status ke DRAFT (ID 1)
                $data['id_status'] = Sop::STATUS_DRAFT;
                
                // Update Record secara manual (BYPASS validasi mutateFormDataBeforeSave)
                $this->record->update($data);
                
                // Simpan Relasi
                $this->form->model($this->record)->saveRelationships();
                
                Notification::make()->title('Draft Disimpan')->success()->send();
                
                // Redirect ke index
                $this->redirect($this->getResource()::getUrl('index'));
            });
    }

    // --- 3. NOTIFIKASI SETELAH SIMPAN ---
    // protected function afterSave(): void
    // {
    //     $sop = $this->record;
        
    //     // Hanya kirim notif jika statusnya "Belum Diverifikasi"
    //     if ($sop->id_status == Sop::STATUS_BELUM_DIVERIFIKASI) {
    //         $verifikators = Sop::getVerifikators();
            
    //         foreach ($verifikators as $verifikator) {
    //             // 1. Simpan ke Database Custom (tb_notifikasi)
    //             Notifikasi::create([
    //                 'id_user'   => $verifikator->id_user,
    //                 'id_sop'    => $sop->id_sop,
    //                 'judul'     => 'Revisi SOP Masuk',
    //                 'isi_notif' => "SOP '{$sop->judul_sop}' telah diperbarui dan menunggu verifikasi.",
    //                 'is_read'   => false,
    //                 'created_by'=> auth()->user()->nama_lengkap,
    //             ]);

    //             // 2. Kirim Notifikasi Lonceng (Realtime Polling)
    //             Notification::make()
    //                 ->title('Revisi SOP Masuk')
    //                 ->body("SOP '{$sop->judul_sop}' telah diperbarui oleh pengusul.")
    //                 ->warning() // Warna kuning/orange
    //                 ->actions([
    //                     Action::make('view')
    //                         ->label('Periksa')
    //                         ->url('/admin/sops/' . $sop->id_sop . '/edit')
    //                         ->markAsRead(),
    //                 ])
    //                 ->sendToDatabase($verifikator);
    //         }
    //     }
    // }
}