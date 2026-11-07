<?php

namespace App\Filament\App\Resources\GarmentTypeResource\Pages;

use App\Filament\App\Resources\GarmentTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListGarmentTypes extends ListRecords
{
    protected static string $resource = GarmentTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
