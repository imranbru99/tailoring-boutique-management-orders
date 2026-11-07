<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAttendance;
use App\Models\User;
use App\Services\AttendanceHubService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function __construct(
        protected AttendanceHubService $attendanceService
    ) {}

    public function punch(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'type' => 'required|in:check_in,check_out',
            'device_uid' => 'nullable|string|max:100',
            'device_ip' => 'nullable|ip',
            'verification_mode' => 'nullable|string|max:50',
            'timestamp' => 'nullable|date',
        ]);

        $targetUser = !empty($validated['user_id'])
            ? User::where('tenant_id', $tenantId)->findOrFail($validated['user_id'])
            : $request->user();

        $time = !empty($validated['timestamp']) ? \Carbon\Carbon::parse($validated['timestamp']) : now();

        $record = $this->attendanceService->recordPunch(
            $targetUser,
            $validated['type'],
            $time->format('H:i:s'),
            $time->toDateString(),
            $validated['verification_mode'] ?? 'biometric_terminal'
        );

        return response()->json([
            'message' => "Successfully recorded {$validated['type']}.",
            'record' => $record,
        ]);
    }

    public function today(Request $request): JsonResponse
    {
        $tenantId = $request->user()->tenant_id;

        $records = EmployeeAttendance::where('tenant_id', $tenantId)
            ->where('date', now()->toDateString())
            ->with(['user', 'shift'])
            ->get();

        return response()->json([
            'date' => now()->toDateString(),
            'attendance' => $records,
        ]);
    }
}
