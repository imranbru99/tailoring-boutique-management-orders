<?php

namespace App\Filament\Customer\Resources\MyOrderResource\Pages;

use App\Filament\Customer\Resources\MyOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListMyOrders extends ListRecords
{
    protected static string $resource = MyOrderResource::class;
}
