<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\Request;

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
        $attendanceHistory = collect();
        $leaveQuota = null;
        $leaveRequests = collect();

        if ($employee) {
            $today = Attendance::where('employee_id', $employee->id)->where('date', now()->toDateString())->first();
            $attendanceHistory = Attendance::where('employee_id', $employee->id)
                ->where('date', '<', now()->toDateString())
                ->orderByDesc('date')
                ->limit(7)
                ->get();

            $leaveQuota = $this->leaveRequestService->annualQuota($employee, now()->year);
            $leaveRequests = LeaveRequest::where('employee_id', $employee->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('portal-karyawan.index', compact('employee', 'user', 'today', 'attendanceHistory', 'leaveQuota', 'leaveRequests'));
    }
}
