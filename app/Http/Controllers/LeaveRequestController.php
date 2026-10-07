<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\LeaveRequest;
use App\Services\LeaveRequestService;
use Illuminate\Http\Request;
use RuntimeException;

class LeaveRequestController extends Controller
{
    public function __construct(private LeaveRequestService $leaveRequestService)
    {
    }

    public function self(Request $request)
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            return view('leave-requests.not-linked');
        }

        $quota = $this->leaveRequestService->annualQuota($employee, now()->year);
        $requests = LeaveRequest::where('employee_id', $employee->id)->orderByDesc('created_at')->get();

        return view('leave-requests.self', compact('employee', 'quota', 'requests'));
    }

    public function store(Request $request)
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            abort(403, 'Akun Anda belum ditautkan ke data Karyawan.');
        }

        $data = $request->validate([
            'type' => ['required', 'in:'.implode(',', LeaveRequest::TYPES)],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date', 'after_or_equal:start_date'],
            'reason' => ['nullable', 'string'],
        ]);

        try {
            $this->leaveRequestService->submit($data, $employee, $request->user(), $request);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('status', $e->getMessage());
        }

        return redirect()->route('leave-requests.self')->with('status', 'Pengajuan berhasil dikirim, menunggu persetujuan HR.');
    }

    public function cancel(Request $request, LeaveRequest $leaveRequest)
    {
        $employee = $request->user()->employee;
        $isOwner = $employee && $leaveRequest->employee_id === $employee->id && $leaveRequest->status === 'PENDING';
        $canManage = $request->user()->hasPermission('leave.manage');

        if (! $isOwner && ! $canManage) {
            abort(403);
        }

        try {
            $this->leaveRequestService->cancel($leaveRequest, $request->user(), $request);
        } catch (RuntimeException $e) {
            return back()->with('status', $e->getMessage());
        }

        return back()->with('status', 'Pengajuan berhasil dibatalkan.');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', LeaveRequest::class);

        $requests = LeaveRequest::with('employee')
            ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->integer('employee_id')))
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(30)
            ->withQueryString();

        $employees = Employee::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('leave-requests.index', compact('requests', 'employees'));
    }

    public function approve(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorize('update', LeaveRequest::class);

        try {
            $this->leaveRequestService->approve($leaveRequest, $request->user(), $request);
        } catch (RuntimeException $e) {
            return back()->with('status', $e->getMessage());
        }

        return back()->with('status', 'Pengajuan disetujui.');
    }

    public function reject(Request $request, LeaveRequest $leaveRequest)
    {
        $this->authorize('update', LeaveRequest::class);

        $data = $request->validate(['rejection_reason' => ['required', 'string']]);

        try {
            $this->leaveRequestService->reject($leaveRequest, $data['rejection_reason'], $request->user(), $request);
        } catch (RuntimeException $e) {
            return back()->with('status', $e->getMessage());
        }

        return back()->with('status', 'Pengajuan ditolak.');
    }
}
