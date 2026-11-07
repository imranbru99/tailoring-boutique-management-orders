<?php

namespace App\Filament\App\Resources\GarmentTypeResource\Pages;

use App\Filament\App\Resources\GarmentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditGarmentType extends EditRecord
{
    protected static string $resource = GarmentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
