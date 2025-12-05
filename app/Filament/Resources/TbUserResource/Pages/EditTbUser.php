<?php

namespace App\Filament\Resources\TbUserResource\Pages;

use App\Filament\Resources\TbUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTbUser extends EditRecord
{
    protected static string $resource = TbUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
