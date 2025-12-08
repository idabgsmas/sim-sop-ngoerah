<?php

namespace App\Filament\Resources\TbUserResource\Pages;

use App\Filament\Resources\TbUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTbUsers extends ListRecords
{
    protected static string $resource = TbUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah User Baru'),
        ];
    }
}
