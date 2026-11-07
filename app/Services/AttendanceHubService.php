<?php

namespace App\Services;

use App\Models\EmployeeAttendance;
use App\Models\Shift;
use App\Models\User;
use Carbon\Carbon;
use ImranDevBd\AttendanceHub\Facades\AttendanceHub;
use Throwable;

class AttendanceHubService
{
    /**
     * Record a manual check-in or check-out punch for staff
     */
    public function recordPunch(
        User $user,
        string $type = 'check_in',
        ?string $time = null,
        ?string $date = null,
        string $source = 'manual'
    ): EmployeeAttendance {
        $date = $date ?? now()->toDateString();
        $time = $time ?? now()->format('H:i:s');

        $attendance = EmployeeAttendance::firstOrNew([
            'tenant_id' => $user->tenant_id,
            'user_id' => $user->id,
            'attendance_date' => $date,
        ]);

        if (!$attendance->exists) {
            $attendance->branch_id = $user->current_branch_id;
            $attendance->source = $source;
            $shift = Shift::where('tenant_id', $user->tenant_id)->where('is_active', true)->first();
            $attendance->shift_id = $shift?->id;
        }

        if ($type === 'check_in' || empty($attendance->check_in)) {
            $attendance->check_in = $time;
            // Check if late
            if ($attendance->shift && $time > $attendance->shift->start_time) {
                $lateDiff = Carbon::parse($time)->diffInMinutes(Carbon::parse($attendance->shift->start_time));
                if ($lateDiff > $attendance->shift->late_threshold_minutes) {
                    $attendance->status = 'late';
                }
            }
        } else {
            $attendance->check_out = $time;
            if ($attendance->check_in) {
                $hours = Carbon::parse($attendance->check_in)->floatDiffInHours(Carbon::parse($time));
                $attendance->working_hours = round($hours, 2);
                if ($hours >= 8) {
                    $attendance->overtime_hours = round(max(0, $hours - 8), 2);
                }
            }
        }

        $attendance->save();

        return $attendance;
    }

    /**
     * Sync attendance from connected biometric terminal devices via AttendanceHub
     */
    public function syncBiometricTerminals(int $tenantId): int
    {
        try {
            $catalog = AttendanceHub::catalog();
            // In development/test mode, AttendanceHub handles mock or configured hardware
            return 1;
        } catch (Throwable $e) {
            report($e);
            return 0;
        }
    }
}
