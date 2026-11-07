<?php

namespace App\Filament\App\Resources\MeasurementProfileResource\Pages;

use App\Filament\App\Resources\MeasurementProfileResource;
use App\Models\MeasurementVersion;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditMeasurementProfile extends EditRecord
{
    protected static string $resource = MeasurementProfileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        // Increment version number and store historical revision
        $newVersion = $this->record->version + 1;
        $this->record->updateQuietly(['version' => $newVersion]);

        MeasurementVersion::create([
            'measurement_profile_id' => $this->record->id,
            'version_number' => $newVersion,
            'unit' => $this->record->unit,
            'values' => $this->record->values ?? [],
            'changed_by' => auth()->id(),
            'change_reason' => 'Measurement profile updated',
        ]);
    }
}
