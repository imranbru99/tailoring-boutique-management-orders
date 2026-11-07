<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\MeasurementProfile;
use App\Models\MeasurementTemplate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MeasurementController extends Controller
{
    public function index(Request $request, int $customerId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $customer = Customer::where('tenant_id', $tenantId)->findOrFail($customerId);
        $profiles = $customer->measurementProfiles()->with('template')->get();

        return response()->json([
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->full_name,
                'code' => $customer->customer_code,
            ],
            'profiles' => $profiles,
        ]);
    }

    public function store(Request $request, int $customerId): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;
        $customer = Customer::where('tenant_id', $tenantId)->findOrFail($customerId);

        $validated = $request->validate([
            'template_id' => 'required|exists:measurement_templates,id',
            'profile_name' => 'required|string|max:100',
            'values' => 'required|array',
            'posture' => 'nullable|string|max:100',
            'body_shape' => 'nullable|string|max:100',
            'notes' => 'nullable|string',
            'is_default' => 'nullable|boolean',
        ]);

        if (!empty($validated['is_default'])) {
            $customer->measurementProfiles()->update(['is_default' => false]);
        }

        $profile = MeasurementProfile::create([
            'tenant_id' => $tenantId,
            'customer_id' => $customer->id,
            'template_id' => $validated['template_id'],
            'profile_name' => $validated['profile_name'],
            'values' => $validated['values'],
            'posture' => $validated['posture'] ?? null,
            'body_shape' => $validated['body_shape'] ?? null,
            'notes' => $validated['notes'] ?? null,
            'is_default' => (bool)($validated['is_default'] ?? false),
            'measured_by_user_id' => $request->user()->id,
            'measured_at' => now(),
        ]);

        return response()->json([
            'message' => 'Measurement profile recorded successfully.',
            'profile' => $profile->load('template'),
        ], 201);
    }
}
