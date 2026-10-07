<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function create(array $data, User $actor, Request $request): Employee
    {
        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('employees/photos', 'public');
        }

        $isActive = isset($data['is_active']) ? filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN) : true;

        $employee = Employee::create([
            'employee_number' => ($data['employee_number'] ?? null) ?: $this->generateEmployeeNumber(),
            'name' => $data['name'],
            'nik_ktp' => $data['nik_ktp'] ?? null,
            'gender' => $data['gender'] ?? null,
            'birth_place' => $data['birth_place'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'address' => $data['address'] ?? null,
            'phone' => $data['phone'] ?? null,
            'email' => $data['email'] ?? null,
            'no_jmo' => $data['no_jmo'] ?? null,
            'no_kpj' => $data['no_kpj'] ?? null,
            'photo_path' => $photoPath,
            'position' => $data['position'] ?? null,
            'department' => $data['department'] ?? 'OTHER',
            'employment_status' => $data['employment_status'] ?? 'TETAP',
            'join_date' => $data['join_date'] ?? null,
            'contract_end_date' => $data['contract_end_date'] ?? null,
            'salary' => $data['salary'] ?? null,
            'resign_date' => $data['resign_date'] ?? null,
            'user_id' => $data['user_id'] ?? null,
            'notes' => $data['notes'] ?? null,
            'is_active' => $isActive,
            'created_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'EMPLOYEE',
            action: 'CREATE_EMPLOYEE',
            target: 'Employee',
            targetId: $employee->id,
            newValue: $employee->only(['name', 'department', 'position', 'employment_status']),
        );

        return $employee;
    }

    public function update(Employee $employee, array $data, User $actor, Request $request): Employee
    {
        $old = $employee->only(['name', 'nik_ktp', 'phone', 'email', 'position', 'department', 'employment_status', 'join_date', 'resign_date', 'no_jmo', 'no_kpj', 'is_active']);

        if ($request->hasFile('photo')) {
            if ($employee->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($employee->photo_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($employee->photo_path);
            }
            $data['photo_path'] = $request->file('photo')->store('employees/photos', 'public');
        }

        if (array_key_exists('is_active', $data)) {
            $data['is_active'] = filter_var($data['is_active'], FILTER_VALIDATE_BOOLEAN);
        }

        $employee->update($data);

        $new = $employee->only(['name', 'nik_ktp', 'phone', 'email', 'position', 'department', 'employment_status', 'join_date', 'resign_date', 'no_jmo', 'no_kpj', 'is_active']);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'EMPLOYEE',
            action: 'UPDATE_EMPLOYEE',
            target: 'Employee',
            targetId: $employee->id,
            oldValue: $old,
            newValue: $new,
        );

        return $employee;
    }

    public function setActive(Employee $employee, bool $active, User $actor, Request $request): Employee
    {
        $employee->update([
            'is_active' => $active,
            'resign_date' => $active ? null : ($employee->resign_date ?? now()->toDateString()),
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'EMPLOYEE',
            action: $active ? 'ENABLE_EMPLOYEE' : 'DISABLE_EMPLOYEE',
            target: 'Employee',
            targetId: $employee->id,
            newValue: ['is_active' => $active],
        );

        return $employee;
    }

    private function generateEmployeeNumber(): string
    {
        return DB::transaction(function () {
            $prefix = 'EMP-';

            $lastNumber = Employee::where('employee_number', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('employee_number')
                ->value('employee_number');

            $nextSeq = $lastNumber ? ((int) substr($lastNumber, strlen($prefix))) + 1 : 1;

            return $prefix.str_pad((string) $nextSeq, 6, '0', STR_PAD_LEFT);
        });
    }
}
