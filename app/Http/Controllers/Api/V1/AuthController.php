<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        if (!$user->is_active) {
            return response()->json([
                'message' => 'Account is inactive. Please contact your atelier administrator.',
            ], 403);
        }

        $deviceName = $request->device_name ?? 'api-token';
        $token = $user->createToken($deviceName)->plainTextToken;

        return response()->json([
            'message' => 'Authenticated successfully.',
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'tenant_id' => $user->tenant_id,
                'tenant_name' => $user->tenant?->name,
                'branch_id' => $user->current_branch_id,
            ],
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $request->user();

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'job_title' => $user->job_title,
                'phone' => $user->phone,
                'tenant_id' => $user->tenant_id,
                'tenant' => $user->tenant ? [
                    'id' => $user->tenant->id,
                    'name' => $user->tenant->name,
                    'slug' => $user->tenant->slug,
                    'currency' => $user->tenant->currency,
                    'measurement_unit' => $user->tenant->measurement_unit,
                ] : null,
                'current_branch' => $user->currentBranch ? [
                    'id' => $user->currentBranch->id,
                    'name' => $user->currentBranch->name,
                    'code' => $user->currentBranch->code,
                ] : null,
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Token revoked successfully.',
        ]);
    }
}
