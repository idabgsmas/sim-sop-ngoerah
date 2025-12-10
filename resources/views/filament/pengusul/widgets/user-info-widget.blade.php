<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-col gap-y-4 md:flex-row md:items-center md:justify-between">
            
            <div class="flex items-center gap-x-4">
                <div class="p-3 bg-primary-50 rounded-full dark:bg-gray-800">
                    <x-heroicon-o-user-circle class="w-10 h-10 text-primary-600" />
                </div>
                <div>
                    <h2 class="text-lg font-bold text-gray-800 dark:text-gray-100">
                        Selamat Datang, {{ $nama }}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Panel Pengusul Dokumen Standar (SOP)
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 text-sm border-t pt-4 md:border-t-0 md:pt-0 border-gray-100 dark:border-gray-700">
                
                <div class="flex items-center gap-x-3">
                    <div class="p-2 bg-gray-50 rounded-lg dark:bg-gray-800">
                        <x-heroicon-o-building-office class="w-5 h-5 text-gray-500" />
                    </div>
                    <div>
                        <p class="font-medium text-gray-500 text-xs">Unit Pengusul</p>
                        <p class="font-bold text-gray-800 dark:text-gray-200">{{ $unit }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-x-3">
                    <div class="p-2 bg-gray-50 rounded-lg dark:bg-gray-800">
                        <x-heroicon-o-building-library class="w-5 h-5 text-gray-500" />
                    </div>
                    <div>
                        <p class="font-medium text-gray-500 text-xs">Direktorat</p>
                        <p class="font-bold text-gray-800 dark:text-gray-200">{{ $direktorat }}</p>
                    </div>
                </div>

            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>