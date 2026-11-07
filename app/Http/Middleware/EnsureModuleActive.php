<?php

namespace App\Http\Middleware;

use App\Core\Modules\ModuleManager;
use App\Core\Modules\ModuleRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureModuleActive
{
    public function __construct(
        protected ModuleManager $moduleManager
    ) {}

    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        $tenantId = auth()->user()?->tenant_id ?? session('current_tenant_id');

        if (!$this->moduleManager->isEnabled($moduleKey, $tenantId)) {
            $meta = ModuleRegistry::get($moduleKey);
            $moduleName = $meta['name'] ?? ucfirst($moduleKey);

            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'module_disabled',
                    'message' => "The [{$moduleName}] module is currently disabled for this business organization. Activate it from Module Management to unlock this capability.",
                    'module' => $moduleKey,
                ], 403);
            }

            abort(403, "The [{$moduleName}] module is currently disabled for your business. Please enable it in Module Settings.");
        }

        return $next($request);
    }
}
