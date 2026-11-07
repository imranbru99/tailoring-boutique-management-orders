<?php

namespace App\Filament\App\Resources\QualityInspectionResource\Pages;

use App\Filament\App\Resources\QualityInspectionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditQualityInspection extends EditRecord
{
    protected static string $resource = QualityInspectionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
