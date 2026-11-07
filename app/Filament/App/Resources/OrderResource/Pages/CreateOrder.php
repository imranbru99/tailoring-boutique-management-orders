<?php

namespace App\Filament\App\Resources\OrderResource\Pages;

use App\Filament\App\Resources\OrderResource;
use App\Models\MeasurementProfile;
use App\Services\OrderWorkflowService;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function afterCreate(): void
    {
        // Snapshot body measurements into items so historical orders remain immutable
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

        // If created in confirmed state, initialize production job cards
        if ($this->record->status === 'confirmed') {
            app(OrderWorkflowService::class)->transition($this->record, 'confirmed');
        }
    }
}
