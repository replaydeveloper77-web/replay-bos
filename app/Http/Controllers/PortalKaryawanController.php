<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PortalKaryawanController extends Controller
{
    public function __construct(private LeaveRequestService $leaveRequestService)
    {
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $employee = $user->employee;

        $today = null;
        $attendanceHistory = [];
        $leaveQuota = null;
        $leaveRequests = [];

        if ($employee) {
            $todayAtt = Attendance::where('employee_id', $employee->id)->where('date', now()->toDateString())->first();
            if ($todayAtt) {
                $today = [
                    'id' => $todayAtt->id,
                    'is_checked_in' => $todayAtt->isCheckedIn(),
                    'is_checked_out' => $todayAtt->isCheckedOut(),
                    'check_in_at' => $todayAtt->check_in_at?->format('H:i'),
                    'check_out_at' => $todayAtt->check_out_at?->format('H:i'),
                ];
            }

            $attendanceHistory = Attendance::where('employee_id', $employee->id)
                ->where('date', '<', now()->toDateString())
                ->orderByDesc('date')
                ->limit(7)
                ->get()
                ->map(fn ($att) => [
                    'id' => $att->id,
                    'date' => $att->date?->format('d/m/Y'),
                    'check_in_at' => $att->check_in_at?->format('H:i'),
                    'check_out_at' => $att->check_out_at?->format('H:i'),
                ])
                ->all();

            $leaveQuota = $this->leaveRequestService->annualQuota($employee, now()->year);

            $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn ($lr) => [
                    'id' => $lr->id,
                    'type_label' => $lr->typeLabel(),
                    'start_date' => $lr->start_date?->format('d/m'),
                    'end_date' => $lr->end_date?->format('d/m/Y'),
                    'status' => $lr->status,
                    'status_label' => $lr->statusLabel(),
                ])
                ->all();
        }

        return Inertia::render('PortalKaryawan/Index', [
            'employee' => $employee ? [
                'id' => $employee->id,
                'name' => $employee->name,
                'employee_number' => $employee->employee_number,
                'position' => $employee->position ?: 'Staf',
                'department' => ucfirst(strtolower($employee->department ?? '')),
                'employment_status' => ucfirst(strtolower($employee->employment_status ?? '')),
                'nik_ktp' => $employee->nik_ktp ?: '-',
                'gender' => $employee->gender === 'L' ? 'Laki-laki' : ($employee->gender === 'P' ? 'Perempuan' : '-'),
                'birth_place' => $employee->birth_place ?: '-',
                'birth_date' => $employee->birth_date?->format('d/m/Y'),
                'email' => $employee->email ?: '-',
                'phone' => $employee->phone ?: '-',
                'address' => $employee->address ?: '-',
                'no_jmo' => $employee->no_jmo ?: '-',
                'no_kpj' => $employee->no_kpj ?: '-',
                'photo_url' => $employee->photoUrl(),
                'join_date' => $employee->join_date?->format('d F Y') ?: '-',
                'resign_date' => $employee->resign_date?->format('d F Y') ?: ($employee->contract_end_date?->format('d F Y') ?: '-'),
                'is_active' => (bool) $employee->is_active,
            ] : null,
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'username' => $user->username,
                'can_manage_employees' => $user->hasRole('super-admin') || $user->hasRole('hr'),
            ],
            'todayDateFormatted' => now()->translatedFormat('l, d F Y'),
            'today' => $today,
            'attendanceHistory' => $attendanceHistory,
            'leaveQuota' => $leaveQuota,
            'leaveRequests' => $leaveRequests,
        ]);
    }
}
