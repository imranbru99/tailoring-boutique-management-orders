<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $query = Customer::where('tenant_id', $tenantId)
            ->with(['measurementProfiles'])
            ->latest();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('customer_code', 'like', "%{$search}%");
            });
        }

        $customers = $query->paginate($request->get('per_page', 15));

        return response()->json($customers);
    }

    public function store(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $validated = $request->validate([
            'first_name' => 'required|string|max:100',
            'last_name' => 'nullable|string|max:100',
            'phone' => 'required|string|max:30',
            'email' => 'nullable|email|max:150',
            'gender' => 'required|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'preferred_language' => 'nullable|string|max:10',
            'notes' => 'nullable|string',
        ]);

        $code = 'CUST-' . strtoupper(substr(uniqid(), -6));

        $customer = Customer::create(array_merge($validated, [
            'tenant_id' => $tenantId,
            'primary_branch_id' => $request->user()->current_branch_id,
            'customer_code' => $code,
        ]));

        return response()->json([
            'message' => 'Customer created successfully.',
            'customer' => $customer,
        ], 201);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $customer = Customer::where('tenant_id', $tenantId)
            ->with(['measurementProfiles.template', 'orders' => fn($q) => $q->latest()->limit(5)])
            ->findOrFail($id);

        return response()->json(['customer' => $customer]);
    }
}
