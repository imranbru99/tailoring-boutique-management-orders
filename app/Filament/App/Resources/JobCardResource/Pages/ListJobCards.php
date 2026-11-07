<?php

namespace App\Filament\App\Resources\JobCardResource\Pages;

use App\Filament\App\Resources\JobCardResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListJobCards extends ListRecords
{
    protected static string $resource = JobCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
