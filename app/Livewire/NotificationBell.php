<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Notifikasi;
use Illuminate\Support\Facades\Auth;

class NotificationBell extends Component
{
    // Properti Public (Bisa diakses di View)
    public $limit = 5; // Mulai dengan 5 data
    public $selectedNotification = null; // Untuk data di Modal

    // Listener events (jika perlu refresh dari luar)
    protected $listeners = ['refreshNotifications' => '$refresh'];

    // 1. Fungsi Load More (Tampilkan lebih banyak)
    public function loadMore()
    {
        $this->limit += 5;
    }

    // 2. Fungsi Tandai Semua Dibaca
    public function markAllRead()
    {
        Notifikasi::where('id_user', Auth::id())
            ->where('is_read', false)
            ->update(['is_read' => true]);
            
        // Kirim notifikasi toast (opsional)
        // \Filament\Notifications\Notification::make()->title('Semua ditandai sudah dibaca')->success()->send();
    }

    // 3. Fungsi Buka Detail (Modal)
    public function openDetail($id)
    {
        // Ambil data notifikasi
        $this->selectedNotification = Notifikasi::find($id);

        // Jika belum dibaca, tandai baca sekarang
        if ($this->selectedNotification && !$this->selectedNotification->is_read) {
            $this->selectedNotification->update(['is_read' => true]);
        }

        // Buka Modal Filament (ID: detail-notifikasi)
        $this->dispatch('open-modal', id: 'detail-notifikasi');
    }

    // 4. Computed Property: URL Redirect Dinamis
    // Digunakan di tombol "Lihat Dokumen SOP" pada Modal
    public function getRedirectUrlProperty()
    {
        if (!$this->selectedNotification || !$this->selectedNotification->id_sop) {
            return '#';
        }

        $role = Auth::user()->role->nama_role ?? '';
        $sopId = $this->selectedNotification->id_sop;

        // Ambil data SOP untuk cek status
        $sop = \App\Models\Sop::find($sopId);
        if (!$sop) return '#';

        return match ($role) {
            'Verifikator' => route('filament.verifikator.resources.sops.view', $sopId),
            'Pengusul' => match ($sop->id_status) {
                // Jika REVISI (3) -> Masuk mode EDIT
                3 => route('filament.pengusul.resources.sops.edit', $sopId),
                
                // Jika AKTIF (4) atau DITOLAK -> Masuk mode VIEW
                4 => route('filament.pengusul.resources.sops.view', $sopId),
                
                // Default (misal Draft/Belum Verif) -> VIEW saja biar aman
                default => route('filament.pengusul.resources.sops.view', $sopId),
            },
            'Viewer'      => route('filament.viewer.resources.sops.view', $sopId), // Asumsi route viewer
            default       => '#',
        };
    }

    // 5. Render View
    public function render()
    {
        $user = Auth::user();
        
        // Query Dasar
        $query = Notifikasi::where('id_user', $user->id_user)->latest();

        // Hitung Total (untuk cek tombol Load More)
        $totalCount = $query->count();
        
        // Ambil Data Sesuai Limit
        $notifications = $query->take($this->limit)->get();

        // Hitung Unread
        $unreadCount = Notifikasi::where('id_user', $user->id_user)
            ->where('is_read', false)
            ->count();

        return view('livewire.notification-bell', [
            'notifications' => $notifications,
            'unreadCount'   => $unreadCount,
            'totalCount'    => $totalCount,
        ]);
    }
}