<?php

namespace App\Filament\App\Resources\MeasurementProfileResource\Pages;

use App\Filament\App\Resources\MeasurementProfileResource;
use App\Models\MeasurementVersion;
use Filament\Resources\Pages\CreateRecord;

class CreateMeasurementProfile extends CreateRecord
{
    protected static string $resource = MeasurementProfileResource::class;

    protected function afterCreate(): void
    {
        // Record initial version 1
        MeasurementVersion::create([
            'measurement_profile_id' => $this->record->id,
            'version_number' => 1,
            'unit' => $this->record->unit,
            'values' => $this->record->values ?? [],
            'changed_by' => auth()->id(),
            'change_reason' => 'Initial measurement profile setup',
        ]);
    }
}
