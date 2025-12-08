<?php

namespace App\Filament\Verifikator\Resources\SopResource\Pages;

use App\Filament\Verifikator\Resources\SopResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSop extends EditRecord
{
    protected static string $resource = SopResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
