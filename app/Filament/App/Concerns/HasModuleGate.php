<?php

namespace App\Filament\App\Concerns;

use App\Core\Modules\ModuleManager;

trait HasModuleGate
{
    public static function getModuleKey(): ?string
    {
        return static::$moduleKey ?? null;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $key = static::getModuleKey();
        if (!$key) {
            return true;
        }

        return app(ModuleManager::class)->isEnabled($key);
    }

    public static function canViewAny(): bool
    {
        $key = static::getModuleKey();
        if (!$key) {
            return true;
        }

        return app(ModuleManager::class)->isEnabled($key);
    }
}
