<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use RuntimeException;

class LeaveRequestService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function annualQuota(Employee $employee, int $year): array
    {
        $used = LeaveRequest::where('employee_id', $employee->id)
            ->where('type', 'CUTI_TAHUNAN')
            ->where('status', 'APPROVED')
            ->whereYear('start_date', $year)
            ->sum('days');

        $quota = $employee->employment_status === 'TETAP' ? LeaveRequest::ANNUAL_QUOTA_DAYS : 0;

        return ['quota' => $quota, 'used' => (int) $used, 'remaining' => max(0, $quota - $used)];
    }

    public function submit(array $data, Employee $employee, User $actor, Request $request): LeaveRequest
    {
        $startDate = $data['start_date'];
        $endDate = $data['end_date'];
        $days = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

        $hasOverlap = LeaveRequest::where('employee_id', $employee->id)
            ->whereIn('status', ['PENDING', 'APPROVED'])
            ->where('start_date', '<=', $endDate)
            ->where('end_date', '>=', $startDate)
            ->exists();

        if ($hasOverlap) {
            throw new RuntimeException('Sudah ada pengajuan cuti/izin lain yang tanggalnya tumpang tindih.');
        }

        if ($data['type'] === 'CUTI_TAHUNAN') {
            $quota = $this->annualQuota($employee, Carbon::parse($startDate)->year);
            if ($days > $quota['remaining']) {
                throw new RuntimeException("Sisa kuota Cuti Tahunan tidak cukup (sisa {$quota['remaining']} hari, diajukan {$days} hari).");
            }
        }

        $leaveRequest = LeaveRequest::create([
            'employee_id' => $employee->id,
            'type' => $data['type'],
            'start_date' => $startDate,
            'end_date' => $endDate,
            'days' => $days,
            'reason' => $data['reason'] ?? null,
            'status' => 'PENDING',
            'created_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'LEAVE_REQUEST',
            action: 'SUBMIT_LEAVE_REQUEST',
            target: 'LeaveRequest',
            targetId: $leaveRequest->id,
            newValue: $leaveRequest->only(['employee_id', 'type', 'start_date', 'end_date', 'days']),
        );

        return $leaveRequest;
    }

    public function approve(LeaveRequest $leaveRequest, User $actor, Request $request): LeaveRequest
    {
        if ($leaveRequest->status !== 'PENDING') {
            throw new RuntimeException('Hanya pengajuan berstatus Menunggu Persetujuan yang bisa disetujui.');
        }

        $leaveRequest->update([
            'status' => 'APPROVED',
            'approved_by' => $actor->id,
            'approved_at' => now(),
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'LEAVE_REQUEST',
            action: 'APPROVE_LEAVE_REQUEST',
            target: 'LeaveRequest',
            targetId: $leaveRequest->id,
            newValue: ['status' => 'APPROVED'],
        );

        return $leaveRequest;
    }

    public function reject(LeaveRequest $leaveRequest, string $reason, User $actor, Request $request): LeaveRequest
    {
        if ($leaveRequest->status !== 'PENDING') {
            throw new RuntimeException('Hanya pengajuan berstatus Menunggu Persetujuan yang bisa ditolak.');
        }

        $leaveRequest->update([
            'status' => 'REJECTED',
            'approved_by' => $actor->id,
            'approved_at' => now(),
            'rejection_reason' => $reason,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'LEAVE_REQUEST',
            action: 'REJECT_LEAVE_REQUEST',
            target: 'LeaveRequest',
            targetId: $leaveRequest->id,
            newValue: ['status' => 'REJECTED', 'rejection_reason' => $reason],
        );

        return $leaveRequest;
    }

    public function cancel(LeaveRequest $leaveRequest, User $actor, Request $request): LeaveRequest
    {
        if (! in_array($leaveRequest->status, ['PENDING', 'APPROVED'], true)) {
            throw new RuntimeException('Pengajuan ini sudah tidak bisa dibatalkan.');
        }

        $old = $leaveRequest->only(['status']);

        $leaveRequest->update(['status' => 'CANCELLED']);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'LEAVE_REQUEST',
            action: 'CANCEL_LEAVE_REQUEST',
            target: 'LeaveRequest',
            targetId: $leaveRequest->id,
            oldValue: $old,
            newValue: ['status' => 'CANCELLED'],
        );

        return $leaveRequest;
    }
}
