<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use RuntimeException;

class AttendanceService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function checkIn(Employee $employee, ?float $lat, ?float $lng, ?string $notes, User $actor, Request $request): Attendance
    {
        $today = now()->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)->where('date', $today)->first();

        if ($attendance && $attendance->isCheckedIn()) {
            throw new RuntimeException('Sudah check-in hari ini pukul '.$attendance->check_in_at->format('H:i').'.');
        }

        $attendance = $attendance ?? new Attendance(['employee_id' => $employee->id, 'date' => $today]);
        $attendance->check_in_at = now();
        $attendance->check_in_lat = $lat;
        $attendance->check_in_lng = $lng;
        $attendance->notes = $notes ?? $attendance->notes;
        $attendance->save();

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ATTENDANCE',
            action: 'CHECK_IN',
            target: 'Attendance',
            targetId: $attendance->id,
            newValue: $attendance->only(['employee_id', 'date', 'check_in_at', 'check_in_lat', 'check_in_lng']),
        );

        return $attendance;
    }

    public function checkOut(Attendance $attendance, ?float $lat, ?float $lng, ?string $notes, User $actor, Request $request): Attendance
    {
        if (! $attendance->isCheckedIn()) {
            throw new RuntimeException('Belum check-in hari ini, tidak bisa check-out.');
        }

        if ($attendance->isCheckedOut()) {
            throw new RuntimeException('Sudah check-out hari ini pukul '.$attendance->check_out_at->format('H:i').'.');
        }

        $attendance->update([
            'check_out_at' => now(),
            'check_out_lat' => $lat,
            'check_out_lng' => $lng,
            'notes' => $notes ?? $attendance->notes,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ATTENDANCE',
            action: 'CHECK_OUT',
            target: 'Attendance',
            targetId: $attendance->id,
            newValue: $attendance->only(['check_out_at', 'check_out_lat', 'check_out_lng']),
        );

        return $attendance;
    }

    public function correct(Attendance $attendance, array $data, User $actor, Request $request): Attendance
    {
        $old = $attendance->only(['check_in_at', 'check_out_at', 'notes']);
        $datePrefix = $attendance->date->toDateString();

        $attendance->update([
            'check_in_at' => ! empty($data['check_in_at']) ? "{$datePrefix} {$data['check_in_at']}" : null,
            'check_out_at' => ! empty($data['check_out_at']) ? "{$datePrefix} {$data['check_out_at']}" : null,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ATTENDANCE',
            action: 'CORRECT_ATTENDANCE',
            target: 'Attendance',
            targetId: $attendance->id,
            oldValue: $old,
            newValue: $attendance->only(['check_in_at', 'check_out_at', 'notes']),
        );

        return $attendance;
    }
}
