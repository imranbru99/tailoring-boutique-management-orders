<?php

namespace App\Filament\App\Resources\MeasurementProfileResource\Pages;

use App\Filament\App\Resources\MeasurementProfileResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMeasurementProfiles extends ListRecords
{
    protected static string $resource = MeasurementProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
