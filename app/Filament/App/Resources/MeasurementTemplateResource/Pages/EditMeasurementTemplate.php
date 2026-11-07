<?php

namespace App\Filament\App\Resources\MeasurementTemplateResource\Pages;

use App\Filament\App\Resources\MeasurementTemplateResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeasurementTemplate extends EditRecord
{
    protected static string $resource = MeasurementTemplateResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
