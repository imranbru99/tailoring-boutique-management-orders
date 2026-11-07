<?php

namespace App\Filament\App\Resources\AppointmentResource\Pages;

use App\Filament\App\Resources\AppointmentResource;
use App\Models\Appointment;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Validation\ValidationException;

class CreateAppointment extends CreateRecord
{
    protected static string $resource = AppointmentResource::class;

    protected function beforeCreate(): void
    {
        $data = $this->data;
        if (!empty($data['assigned_staff_id']) && !empty($data['appointment_date'])) {
            $conflict = Appointment::where('assigned_staff_id', $data['assigned_staff_id'])
                ->where('appointment_date', $data['appointment_date'])
                ->where('status', '!=', 'cancelled')
                ->where(function ($q) use ($data) {
                    $q->whereBetween('start_time', [$data['start_time'], $data['end_time']])
                      ->orWhereBetween('end_time', [$data['start_time'], $data['end_time']]);
                })
                ->exists();

            if ($conflict) {
                Notification::make()
                    ->title('Schedule Conflict')
                    ->body('The selected tailor/staff already has an active appointment in this time window.')
                    ->danger()
                    ->send();

                throw ValidationException::withMessages([
                    'start_time' => 'Schedule collision with an existing staff booking.',
                ]);
            }
        }
    }
}
