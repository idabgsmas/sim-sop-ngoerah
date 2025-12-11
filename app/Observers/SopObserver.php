<?php

namespace App\Observers;

use App\Models\Sop;
use App\Services\SopNotificationService;

class SopObserver
{
    protected $notificationService;

    public function __construct(SopNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the Sop "created" event. (UNTUK PENGAJUAN BARU)
     */
    public function created(Sop $sop)
    {
        // Jika SOP baru dibuat dan statusnya langsung 'Belum Diverifikasi' (2)
        if ($sop->id_status === Sop::STATUS_BELUM_DIVERIFIKASI) {
            $verifikators = Sop::getVerifikators(); // Ambil semua verifikator
            
            $this->notificationService->send(
                $verifikators,
                'Pengajuan SOP Baru',
                "Unit {$sop->unitKerja->nama_unit} mengajukan dokumen baru: {$sop->judul_sop}",
                $sop,
                'info'
            );
        }
    }

    /**
     * Handle the Sop "updated" event. (UNTUK REVISI & APPROVAL)
     */
    public function updated(Sop $sop)
    {
        // Hanya jalankan jika status berubah
        if ($sop->isDirty('id_status')) {
            $newStatus = $sop->id_status;
            
            // 1. REVISI MASUK (Revisi -> Belum Verif)
            if ($newStatus === Sop::STATUS_BELUM_DIVERIFIKASI) {
                $verifikators = Sop::getVerifikators();
                
                $this->notificationService->send(
                    $verifikators,
                    'Revisi SOP Masuk',
                    "Unit {$sop->unitKerja->nama_unit} telah memperbaiki dokumen: {$sop->judul_sop}",
                    $sop,
                    'info'
                );
            }

            // 2. DIKEMBALIKAN / PERLU REVISI (Belum Verif -> Revisi)
            if ($newStatus === Sop::STATUS_REVISI) {
                $this->notificationService->send(
                    $sop->uploader, // Kirim ke Pengusul
                    'SOP Perlu Revisi',
                    "Dokumen '{$sop->judul_sop}' dikembalikan. Cek catatan verifikator.",
                    $sop,
                    'danger'
                );
            }

            // 3. DISETUJUI (Belum Verif -> Aktif)
            if ($newStatus === Sop::STATUS_AKTIF) {
                $this->notificationService->send(
                    $sop->uploader, // Kirim ke Pengusul
                    'SOP Disetujui',
                    "Selamat! Dokumen '{$sop->judul_sop}' telah diterbitkan dan Aktif.",
                    $sop,
                    'success'
                );
            }
        }
    }
}