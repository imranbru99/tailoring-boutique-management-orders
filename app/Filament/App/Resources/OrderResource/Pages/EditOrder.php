<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Filament\App\Resources\OrderResource;
use App\Models\MeasurementProfile;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function afterSave(): void
    {
        foreach ($this->record->items as $item) {
            if ($item->measurement_profile_id && empty($item->measurement_snapshot)) {
                $profile = MeasurementProfile::find($item->measurement_profile_id);
                if ($profile) {
                    $item->updateQuietly([
                        'measurement_snapshot' => $profile->createSnapshot(),
                    ]);
                }
            }
        }

        $this->record->recalculateTotals();
    }
}
