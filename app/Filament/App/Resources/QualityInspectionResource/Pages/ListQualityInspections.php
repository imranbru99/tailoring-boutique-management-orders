<?php

namespace App\Filament\App\Resources\QualityInspectionResource\Pages;

use App\Filament\App\Resources\QualityInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQualityInspections extends ListRecords
{
    protected static string $resource = QualityInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
