<?php

namespace App\Filament\App\Resources\FabricTypeResource\Pages;

use App\Filament\App\Resources\FabricTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFabricType extends EditRecord
{
    protected static string $resource = FabricTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
