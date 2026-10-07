<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Models\User;
use App\Services\LeaveRequestService;
use Illuminate\Http\Request;

class PortalKaryawanController extends Controller
{
    public function __construct(private LeaveRequestService $leaveRequestService)
    {
    }

    public function index(Request $request)
    {
        $query = Employee::with('user');

        if ($request->filled('q')) {
            $term = '%' . $request->string('q')->trim() . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('position', 'like', $term)
                  ->orWhere('nik_ktp', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('no_jmo', 'like', $term)
                  ->orWhere('no_kpj', 'like', $term)
                  ->orWhere('employee_number', 'like', $term)
                  ->orWhere('phone', 'like', $term)
                  ->orWhere('address', 'like', $term);
            });
        }

        if ($request->filled('status')) {
            if ($request->status === '1') {
                $query->where('is_active', true);
            } elseif ($request->status === '0') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        $employees = $query->orderBy('name')->paginate(15)->withQueryString();

        $currentUser = $request->user();
        $canManage = $currentUser->hasRole('super-admin')
            || $currentUser->hasRole('hr')
            || $currentUser->can('create', Employee::class);

        $users = $canManage ? User::where('is_active', true)->orderBy('name')->get() : collect();

        // Data karyawan akun login saat ini (jika sudah ditautkan)
        $myEmployee = $currentUser->employee;
        $today = null;
        $attendanceHistory = collect();
        $leaveQuota = null;
        $leaveRequests = collect();

        if ($myEmployee) {
            $today = Attendance::where('employee_id', $myEmployee->id)->where('date', now()->toDateString())->first();
            $attendanceHistory = Attendance::where('employee_id', $myEmployee->id)
                ->where('date', '<', now()->toDateString())
                ->orderByDesc('date')
                ->limit(7)
                ->get();
            $leaveQuota = $this->leaveRequestService->annualQuota($myEmployee, now()->year);
            $leaveRequests = LeaveRequest::where('employee_id', $myEmployee->id)
                ->orderByDesc('created_at')
                ->limit(5)
                ->get();
        }

        return view('portal-karyawan.index', compact(
            'employees',
            'canManage',
            'users',
            'myEmployee',
            'today',
            'attendanceHistory',
            'leaveQuota',
            'leaveRequests'
        ));
    }
}
