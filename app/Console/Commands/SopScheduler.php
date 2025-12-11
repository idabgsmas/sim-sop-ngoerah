<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Sop;
use App\Models\TbUser;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class SopScheduler extends Command
{
    protected $signature = 'sop:run-scheduler';
    protected $description = 'Cek Review Tahunan & Warning Expired SOP';

    public function handle()
    {
        $this->info('Menjalankan scheduler SOP...');
        
        // --- BAGIAN A: REVIEW TAHUNAN ---
        
        // 1. Logic Reminder (H-30 s.d H+30)
        // Kirim notifikasi setiap 3 hari jika belum direview
        $reviewSops = Sop::where('id_status', 4) // Aktif
            ->whereNotNull('tgl_review_tahunan')
            ->whereBetween('tgl_review_tahunan', [now()->subDays(30), now()->addDays(30)])
            ->get();

        foreach ($reviewSops as $sop) {
            $tglReview = Carbon::parse($sop->tgl_review_tahunan);
            $diffDays = now()->diffInDays($tglReview, false); // (+) belum lewat, (-) sudah lewat
            
            // Kirim notifikasi setiap kelipatan 3 hari
            if (abs($diffDays) % 3 === 0) {
                $statusMsg = $diffDays >= 0 ? "dalam {$diffDays} hari" : "terlewat " . abs($diffDays) . " hari";
                $this->sendNotification(
                    $sop,
                    'Waktunya Review Tahunan',
                    "SOP '{$sop->judul_sop}' jadwal review {$statusMsg}. Segera konfirmasi di dashboard.",
                    'warning'
                );
            }
        }

        // 2. Logic Auto-Skip (Jika lewat 30 hari tanpa aksi)
        $expiredReviewSops = Sop::where('id_status', 4)
            ->whereNotNull('tgl_review_tahunan')
            ->where('tgl_review_tahunan', '<', now()->subDays(30))
            ->get();

        foreach ($expiredReviewSops as $sop) {
            $currentReview = Carbon::parse($sop->tgl_review_tahunan);
            $nextReview = $currentReview->copy()->addYear();
            $expiredDate = Carbon::parse($sop->tgl_kadaluwarsa);

            // Cek: Apakah tahun depan sudah expired?
            if ($nextReview->gte($expiredDate)) {
                // STOP REVIEW: Fokus ke Expired
                $sop->update(['tgl_review_tahunan' => null]);
                
                $this->sendNotification(
                    $sop,
                    'Review Tahunan Dihentikan',
                    "Masa berlaku SOP '{$sop->judul_sop}' hampir habis. Sistem menghentikan jadwal review. Fokus pada pembaruan/expired.",
                    'danger'
                );
            } else {
                // UPDATE: Geser ke tahun depan (Auto Skip)
                $sop->update(['tgl_review_tahunan' => $nextReview]);
                
                $this->sendNotification(
                    $sop,
                    'Review Tahunan Terlewat (Auto-Update)',
                    "Batas waktu review SOP '{$sop->judul_sop}' habis. Jadwal review otomatis diperbarui ke tahun depan.",
                    'info'
                );
            }
        }

        // --- BAGIAN B: WARNING KADALUWARSA (EXPIRED) ---

        // 3. Logic Warning Expired (H-90, H-30, H-7)
        $warningDays = [90, 30, 7];
        
        // Ambil SOP yang kadaluwarsanya pas di hari H-90, H-30, atau H-7 dari sekarang
        // whereDate memastikan hanya kena sekali pada hari tersebut
        foreach ($warningDays as $days) {
            $expiringSops = Sop::where('id_status', 4)
                ->whereDate('tgl_kadaluwarsa', now()->addDays($days)->toDateString())
                ->get();

            foreach ($expiringSops as $sop) {
                $this->sendNotification(
                    $sop,
                    "Warning: SOP Akan Kadaluwarsa ({$days} Hari)",
                    "SOP '{$sop->judul_sop}' akan tidak berlaku dalam {$days} hari. Segera lakukan revisi total/perpanjangan.",
                    'danger'
                );
            }
        }

        // 4. Logic SUDAH EXPIRED (Hari Ini)
        $justExpiredSops = Sop::where('id_status', 4)
            ->whereDate('tgl_kadaluwarsa', '<', now())
            ->get();

        foreach ($justExpiredSops as $sop) {
            $sop->update(['id_status' => 5]); // Set status ke KADALUWARSA (ID 5)
            
            $this->sendNotification(
                $sop,
                'SOP Telah Kadaluwarsa',
                "Masa berlaku SOP '{$sop->judul_sop}' telah habis. Dokumen kini tidak aktif.",
                'danger'
            );
        }

        $this->info('Scheduler selesai.');
    }

    private function sendNotification($sop, $title, $body, $type)
    {
        $service = new SopNotificationService(); // Instansiasi Service

        // Gabungkan penerima (Pengusul + Verifikator)
        $recipients = collect([$sop->uploader]);
        $verifikators = \App\Models\TbUser::whereHas('role', fn($q) => $q->where('nama_role', 'Verifikator'))->get();
        $recipients = $recipients->merge($verifikators);

        // Panggil fungsi send() yang sudah kita buat pintar tadi
        $service->send($recipients, $title, $body, $sop, $type);
    }
}