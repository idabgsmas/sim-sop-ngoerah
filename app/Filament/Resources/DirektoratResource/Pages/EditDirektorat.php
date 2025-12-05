<?php

namespace App\Filament\Resources\DirektoratResource\Pages;

use App\Filament\Resources\DirektoratResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDirektorat extends EditRecord
{
    protected static string $resource = DirektoratResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
