<?php

namespace App\Filament\Pengusul\Resources\SopResource\Pages;

use App\Filament\Pengusul\Resources\SopResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Sop;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;

class CreateSop extends CreateRecord
{
    protected static string $resource = SopResource::class;

    // 1. Hilangkan tombol "Create" & "Create & Create Another" bawaan
    protected function getFormActions(): array
    {
        return [
            $this->getKirimAction(),       // Tombol Utama (Kirim)
            $this->getSimpanDraftAction(), // Tombol Kedua (Draft)
            $this->getCancelFormAction(),  // Tombol Batal
        ];
    }

    // 2. Logic Tombol "Kirim" (Validasi Ketat)
    protected function getKirimAction(): Actions\Action
    {
        return Actions\Action::make('create')
            ->label('Kirim & Ajukan Verifikasi')
            ->submit('create')
            ->keyBindings(['mod+s'])
            ->color('primary')
            ->action(function () {
                $this->create(another: false); // Panggil fungsi create standard
            });
    }

    // 3. Logic Tombol "Simpan Draft" (Validasi Longgar)
    protected function getSimpanDraftAction(): Actions\Action
    {
        return Actions\Action::make('save_draft')
            ->label('Simpan sebagai Draft')
            ->color('gray')
            ->action(function () {
                $data = $this->form->getState();
                
                // --- PERBAIKAN: PAKSA BONGKAR ARRAY SAMPAI JADI STRING ---
                if (isset($data['dokumen_path'])) {
                    // Selama dia masih array, ambil elemen pertamanya terus menerus
                    while (is_array($data['dokumen_path'])) {
                        $data['dokumen_path'] = reset($data['dokumen_path']);
                    }
                }
                // ---------------------------------------------------------

                // Hapus unitTerkait agar tidak error saat insert ke tb_sop
                if (isset($data['unitTerkait'])) {
                    unset($data['unitTerkait']);
                }
                
                // Set Status Draft
                $data['id_status'] = Sop::STATUS_DRAFT; 

                // Simpan Record
                $this->record = $this->handleRecordCreation($data);
                $this->form->model($this->record)->saveRelationships();
                
                Notification::make()
                    ->title('Disimpan sebagai Draft')
                    ->success()
                    ->send();

                $this->redirect($this->getResource()::getUrl('index'));
            });
    }

    // 4. Intercept proses Create standard untuk set Status "Belum Diverifikasi" + Validasi Manual
    protected function handleRecordCreation(array $data): Model
    {
        // Jika tombol yang ditekan adalah 'create' (Kirim), status = Belum Diverifikasi
        // Jika draft, status sudah diset di action draft di atas.
        
        // Cek status, jika bukan draft, berarti sedang submit
        if (!isset($data['id_status']) || $data['id_status'] != Sop::STATUS_DRAFT) {
            
            // Lakukan VALIDASI MANUAL di sini karena di Form Schema kita buat nullable
            $this->validate([
                'data.judul_sop' => 'required',
                'data.deskripsi_sop' => 'required',
                'data.nomor_sop' => 'required',
                'data.kategori_sop' => 'required',
                'data.dokumen_path' => 'required',
                'data.tgl_pengesahan' => 'required',
                'data.tgl_berlaku' => 'required',
                'data.tgl_kadaluwarsa' => 'required',
                'data.tgl_review_tahunan' => 'required',
            ]);
            
            // Set Status ke BELUM DIVERIFIKASI (ID 2)
            $data['id_status'] = Sop::STATUS_BELUM_DIVERIFIKASI; 
        }

        return parent::handleRecordCreation($data);
    }
    
    // 5. Notifikasi ke Verifikator setelah Create
    protected function afterCreate(): void
    {
        $sop = $this->record;

        // Hanya kirim notifikasi jika status bukan Draft
        if ($sop->id_status == Sop::STATUS_BELUM_DIVERIFIKASI) {
            
            $verifikators = Sop::getVerifikators(); // Ambil list verifikator
            
            Notification::make()
                ->title('Pengajuan SOP Baru')
                ->body("SOP '{$sop->judul_sop}' diajukan oleh unit " . auth()->user()->unitKerja->first()->nama_unit)
                ->actions([
                    Actions\Action::make('view')
                        ->url('/admin/sops/' . $sop->id_sop) // URL Verifikator (Panel Admin)
                        ->button(),
                ])
                ->sendToDatabase($verifikators); // Kirim notif database
        }
    }
}
