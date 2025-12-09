<?php

namespace App\Filament\Pengusul\Resources\SopResource\Pages;

use App\Filament\Pengusul\Resources\SopResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSops extends ListRecords
{
    protected static string $resource = SopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
            ->label('Tambah SOP Baru')
            ->icon('heroicon-o-document-plus'),
        ];
    }
}
