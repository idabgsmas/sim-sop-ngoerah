<?php

namespace App\Filament\Resources\TbUserResource\Pages;

use App\Filament\Resources\TbUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTbUser extends CreateRecord
{
    protected static string $resource = TbUserResource::class;
}
