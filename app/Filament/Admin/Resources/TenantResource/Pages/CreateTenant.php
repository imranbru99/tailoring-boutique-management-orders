<?php

namespace App\Filament\Admin\Resources\TenantResource\Pages;

use App\Core\Modules\ModuleManager;
use App\Filament\Admin\Resources\TenantResource;
use App\Models\Branch;
use Filament\Resources\Pages\CreateRecord;

class CreateTenant extends CreateRecord
{
    protected static string $resource = TenantResource::class;

    protected function afterCreate(): void
    {
        // 1. Create primary atelier branch
        Branch::create([
            'tenant_id' => $this->record->id,
            'name' => "{$this->record->name} - Flagship Atelier",
            'code' => 'MAIN',
            'is_main' => true,
            'is_active' => true,
        ]);

        // 2. Initialize default recommended modules
        app(ModuleManager::class)->syncDefaultModules($this->record->id, $this->record->business_type);
    }
}
