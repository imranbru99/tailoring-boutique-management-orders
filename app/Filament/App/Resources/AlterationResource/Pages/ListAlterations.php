<?php

namespace App\Filament\App\Resources\AlterationResource\Pages;

use App\Filament\App\Resources\AlterationResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAlterations extends ListRecords
{
    protected static string $resource = AlterationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
