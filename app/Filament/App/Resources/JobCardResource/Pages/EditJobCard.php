<?php

namespace App\Filament\App\Resources\JobCardResource\Pages;

use App\Filament\App\Resources\JobCardResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditJobCard extends EditRecord
{
    protected static string $resource = JobCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
