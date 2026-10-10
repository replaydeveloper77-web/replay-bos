<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;

class AttendanceService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function savePhoto(mixed $photoData, string $type, int $employeeId): ?string
    {
        if (empty($photoData)) {
            return null;
        }

        $folder = 'attendances/' . now()->format('Y-m');
        $filename = "{$type}_{$employeeId}_" . now()->format('Ymd_His') . '_' . Str::random(6) . '.jpg';
        $fullPath = "{$folder}/{$filename}";

        if ($photoData instanceof \Illuminate\Http\UploadedFile) {
            return $photoData->storeAs($folder, $filename, 'public');
        }

        if (is_string($photoData) && str_starts_with($photoData, 'data:image')) {
            $parts = explode(',', $photoData, 2);
            if (isset($parts[1])) {
                $decoded = base64_decode($parts[1]);
                if ($decoded !== false) {
                    Storage::disk('public')->put($fullPath, $decoded);
                    return $fullPath;
                }
            }
        }

        return null;
    }

    public function checkIn(
        Employee $employee,
        ?float $lat,
        ?float $lng,
        ?string $notes,
        User $actor,
        Request $request,
        mixed $photo = null,
        ?string $address = null
    ): Attendance {
        $today = now()->toDateString();

        $attendance = Attendance::where('employee_id', $employee->id)->whereDate('date', $today)->first();

        if ($attendance && $attendance->isCheckedIn()) {
            throw new RuntimeException('Sudah check-in hari ini pukul '.$attendance->check_in_at->format('H:i').'.');
        }

        $photoPath = $this->savePhoto($photo, 'checkin', $employee->id);

        $attendance = $attendance ?? new Attendance(['employee_id' => $employee->id, 'date' => $today]);
        $attendance->check_in_at = now();
        $attendance->check_in_lat = $lat;
        $attendance->check_in_lng = $lng;
        if ($photoPath) {
            $attendance->check_in_photo_path = $photoPath;
        }
        if ($address) {
            $attendance->check_in_address = $address;
        }
        $attendance->notes = $notes ?? $attendance->notes;
        $attendance->save();

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ATTENDANCE',
            action: 'CHECK_IN',
            target: 'Attendance',
            targetId: $attendance->id,
            newValue: $attendance->only(['employee_id', 'date', 'check_in_at', 'check_in_lat', 'check_in_lng', 'check_in_address', 'check_in_photo_path']),
        );

        return $attendance;
    }

    public function checkOut(
        Attendance $attendance,
        ?float $lat,
        ?float $lng,
        ?string $notes,
        User $actor,
        Request $request,
        mixed $photo = null,
        ?string $address = null
    ): Attendance {
        if (! $attendance->isCheckedIn()) {
            throw new RuntimeException('Belum check-in hari ini, tidak bisa check-out.');
        }

        if ($attendance->isCheckedOut()) {
            throw new RuntimeException('Sudah check-out hari ini pukul '.$attendance->check_out_at->format('H:i').'.');
        }

        $photoPath = $this->savePhoto($photo, 'checkout', $attendance->employee_id);

        $attendance->check_out_at = now();
        $attendance->check_out_lat = $lat;
        $attendance->check_out_lng = $lng;
        if ($photoPath) {
            $attendance->check_out_photo_path = $photoPath;
        }
        if ($address) {
            $attendance->check_out_address = $address;
        }
        $attendance->notes = $notes ?? $attendance->notes;
        $attendance->save();

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ATTENDANCE',
            action: 'CHECK_OUT',
            target: 'Attendance',
            targetId: $attendance->id,
            newValue: $attendance->only(['check_out_at', 'check_out_lat', 'check_out_lng', 'check_out_address', 'check_out_photo_path']),
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
