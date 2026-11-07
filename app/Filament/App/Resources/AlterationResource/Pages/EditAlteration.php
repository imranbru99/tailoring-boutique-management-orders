<?php

namespace App\Filament\App\Resources\AlterationResource\Pages;

use App\Filament\App\Resources\AlterationResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAlteration extends EditRecord
{
    protected static string $resource = AlterationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
