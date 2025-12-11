<div style="display: flex; align-items: center;"> 

    {{-- DROPDOWN TRIGGER --}}
    <x-filament::dropdown placement="bottom-end" width="md">

        <x-slot name="trigger">
            <div class="relative group" style="margin-right: 1rem; cursor: pointer; display: flex; align-items: center;">
                {{-- POLL: Cek notifikasi baru setiap 10 detik --}}
                <div wire:poll.10s class="flex items-center">
                    
                    {{-- 1. IKON LONCENG --}}
                    <div class="p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition">
                        <x-heroicon-o-bell class="w-5 h-5 text-gray-500 group-hover:text-primary-600 transition" />
                    </div>

                    {{-- 2. BADGE MERAH --}}
                    @if($unreadCount > 0)
                        <span style="
                            position: absolute;
                            top: 2px;
                            right: 2px;
                            background-color: #ef4444;
                            color: white;
                            border-radius: 9999px;
                            height: 16px;
                            min-width: 16px;
                            padding: 0 3px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 9px;
                            font-weight: 800;
                            border: 2px solid white;
                            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
                            z-index: 10;
                        ">
                            {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                        </span>
                    @endif
                </div>
            </div>
        </x-slot>

        {{-- HEADER --}}
        <x-filament::dropdown.header>
            <div class="flex justify-between items-center w-full">
                <span class="font-bold text-sm">Notifikasi</span>
                @if($unreadCount > 0)
                    <button wire:click="markAllRead" class="text-xs text-primary-600 hover:underline cursor-pointer font-medium">
                        Tandai semua dibaca
                    </button>
                @endif
            </div>
        </x-filament::dropdown.header>

        {{-- LIST NOTIFIKASI --}}
        <div style="max-height: 350px; overflow-y: auto;" class="divide-y divide-gray-100 dark:divide-gray-700">
            <x-filament::dropdown.list>
                @forelse($notifications as $notif)
                    <x-filament::dropdown.list.item
                        wire:click="openDetail({{ $notif->id_notifikasi }})"
                        icon="{{ $notif->is_read ? 'heroicon-m-envelope-open' : 'heroicon-s-envelope' }}"
                        color="{{ $notif->is_read ? 'gray' : 'primary' }}"
                        tag="button" 
                    >
                        {{-- Judul --}}
                        <div class="{{ $notif->is_read ? 'font-normal text-gray-600' : 'font-bold text-gray-900 dark:text-white' }}">
                            {{ $notif->judul }}
                        </div>

                        {{-- Preview Pesan (Database Column: isi_notif) --}}
                        <div class="text-xs text-gray-500 mt-1 line-clamp-1">
                            {{ \Illuminate\Support\Str::limit($notif->isi_notif, 40) }}
                        </div>

                        {{-- Waktu --}}
                        <div class="text-[10px] text-gray-400 mt-1 text-right">
                            {{ $notif->created_at->diffForHumans() }}
                        </div>
                    </x-filament::dropdown.list.item>
                @empty
                    <div class="p-4 text-center text-gray-500 text-sm">
                        Tidak ada notifikasi.
                    </div>
                @endforelse
            </x-filament::dropdown.list>
        </div>

        {{-- FOOTER LOAD MORE --}}
        @if($notifications->count() < $totalCount)
            <div class="p-2 border-t border-gray-100 dark:border-gray-700 text-center">
                <button wire:click="loadMore" class="text-xs font-semibold text-gray-500 hover:text-gray-900 w-full py-1">
                    Tampilkan lebih banyak...
                </button>
            </div>
        @endif

    </x-filament::dropdown>

    {{-- MODAL POP-UP DETAIL --}}
    {{-- Menggunakan id="detail-notifikasi" agar match dengan $this->dispatch('open-modal') --}}
    <x-filament::modal id="detail-notifikasi" width="lg" alignment="center">
        @if($selectedNotification)
            <x-slot name="heading">
                {{ $selectedNotification->judul }}
            </x-slot>

            <x-slot name="description">
                Diterima: {{ $selectedNotification->created_at->format('d F Y, H:i') }}
            </x-slot>

            <div class="mt-4 p-4 bg-gray-50 dark:bg-gray-900 rounded-lg border border-gray-200 dark:border-gray-700 text-sm leading-relaxed text-gray-800 dark:text-gray-200">
                {{-- Database Column: isi_notif --}}
                {!! nl2br(e($selectedNotification->isi_notif)) !!}
            </div>

            <x-slot name="footer">
                <div class="flex justify-between items-center w-full">
                    {{-- Tombol Tutup --}}
                    <x-filament::button color="gray" x-on:click="$dispatch('close-modal', { id: 'detail-notifikasi' })">
                        Tutup
                    </x-filament::button>

                    {{-- Tombol Lihat Dokumen (URL Dinamis) --}}
                    @if($selectedNotification->id_sop)
                        <x-filament::button
                            tag="a"
                            href="{{ $this->redirectUrl }}" 
                            icon="heroicon-m-arrow-right"
                            icon-position="after"
                        >
                            Lihat Dokumen SOP
                        </x-filament::button>
                    @endif
                </div>
            </x-slot>
        @endif
    </x-filament::modal>

</div>