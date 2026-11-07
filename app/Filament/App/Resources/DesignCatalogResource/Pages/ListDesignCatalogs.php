<?php

namespace App\Filament\App\Resources\DesignCatalogResource\Pages;

use App\Filament\App\Resources\DesignCatalogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDesignCatalogs extends ListRecords
{
    protected static string $resource = DesignCatalogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
