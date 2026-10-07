<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateAttendanceRequest;
use App\Models\Attendance;
use App\Models\Employee;
use App\Services\AttendanceService;
use Illuminate\Http\Request;
use RuntimeException;

class AttendanceController extends Controller
{
    public function __construct(private AttendanceService $attendanceService)
    {
    }

    public function self(Request $request)
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            return view('attendance.not-linked');
        }

        $today = Attendance::where('employee_id', $employee->id)->where('date', now()->toDateString())->first();
        $history = Attendance::where('employee_id', $employee->id)
            ->where('date', '<', now()->toDateString())
            ->orderByDesc('date')
            ->limit(14)
            ->get();

        return view('attendance.self', compact('employee', 'today', 'history'));
    }

    public function checkIn(Request $request)
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            abort(403, 'Akun Anda belum ditautkan ke data Karyawan.');
        }

        $data = $request->validate([
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        try {
            $this->attendanceService->checkIn($employee, $data['lat'] ?? null, $data['lng'] ?? null, $data['notes'] ?? null, $request->user(), $request);
        } catch (RuntimeException $e) {
            return back()->with('status', $e->getMessage());
        }

        return back()->with('status', 'Check-in berhasil dicatat.');
    }

    public function checkOut(Request $request)
    {
        $employee = $request->user()->employee;

        if (! $employee) {
            abort(403, 'Akun Anda belum ditautkan ke data Karyawan.');
        }

        $data = $request->validate([
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
            'notes' => ['nullable', 'string'],
        ]);

        $attendance = Attendance::where('employee_id', $employee->id)->where('date', now()->toDateString())->first();

        if (! $attendance) {
            return back()->with('status', 'Belum check-in hari ini, tidak bisa check-out.');
        }

        try {
            $this->attendanceService->checkOut($attendance, $data['lat'] ?? null, $data['lng'] ?? null, $data['notes'] ?? null, $request->user(), $request);
        } catch (RuntimeException $e) {
            return back()->with('status', $e->getMessage());
        }

        return back()->with('status', 'Check-out berhasil dicatat.');
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Attendance::class);

        $attendances = Attendance::with('employee')
            ->when($request->filled('employee_id'), fn ($query) => $query->where('employee_id', $request->integer('employee_id')))
            ->when($request->filled('date_from'), fn ($query) => $query->where('date', '>=', $request->date('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->where('date', '<=', $request->date('date_to')))
            ->orderByDesc('date')
            ->paginate(30)
            ->withQueryString();

        $employees = Employee::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('attendance.index', compact('attendances', 'employees'));
    }

    public function edit(Attendance $attendance)
    {
        $this->authorize('update', Attendance::class);

        $attendance->load('employee');

        return view('attendance.edit', compact('attendance'));
    }

    public function update(UpdateAttendanceRequest $request, Attendance $attendance)
    {
        $this->attendanceService->correct($attendance, $request->validated(), $request->user(), $request);

        return redirect()->route('attendance.index')->with('status', 'Data absensi berhasil dikoreksi.');
    }
}
