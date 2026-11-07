<?php

namespace App\Filament\App\Resources\DeliveryResource\Pages;

use App\Filament\App\Resources\DeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDelivery extends EditRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
