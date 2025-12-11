<?php

namespace App\Services;

use App\Models\Sop;
use App\Models\Notifikasi; // Model Custom Anda
use Filament\Notifications\Notification; // Filament Native
use Filament\Notifications\Actions\Action;
use Illuminate\Support\Collection;

class SopNotificationService
{
    /**
     * Fungsi utama untuk mengirim notifikasi ke DUA TEMPAT (Filament & Custom Table)
     */
    public function send($recipients, string $title, string $body, Sop $sop, string $statusType = 'info')
    {
        // Pastikan recipients adalah collection
        if (!($recipients instanceof Collection)) {
            $recipients = collect([$recipients]);
        }

        foreach ($recipients as $user) {
            if (!$user) continue;

            // 1. SIMPAN KE TABEL CUSTOM (tb_notifikasi) - Sesuai Request Anda
            Notifikasi::create([
                'id_user'   => $user->id_user,
                'id_sop'    => $sop->id_sop,
                'judul'     => $title,
                'isi_notif' => $body,
                'is_read'   => false,
            ]);

            // 2. KIRIM KE FILAMENT (Untuk UI Lonceng & Toast)
            // Kita butuh ini agar user bisa klik tombol "Lihat"
            $filamentNotif = Notification::make()
                ->title($title)
                ->body($body)
                ->status($statusType) // success, warning, danger, info
                ->actions([
                    Action::make('view')
                        ->label('Lihat Detail')
                        ->url($this->getSopUrl($sop, $user))
                        ->markAsRead(),
                ]);

            // Kirim ke Database Filament (agar muncul di lonceng)
            $filamentNotif->sendToDatabase($user);
            
            // Kirim ke Toast (Pop-up sementara)
            // $filamentNotif->send(); 
        }
    }

    /**
     * Helper untuk menentukan URL redirect berdasarkan Role User
     */
    private function getSopUrl(Sop $sop, $user)
    {
        // Sesuaikan route berdasarkan panel user penerima
        $role = $user->role->nama_role ?? '';

        return match ($role) {
            'Verifikator' => route('filament.verifikator.resources.sops.view', $sop), // Ke halaman View
            'Pengusul'    => route('filament.pengusul.resources.sops.edit', $sop),    // Ke halaman Edit
            default       => '#',
        };
    }
}