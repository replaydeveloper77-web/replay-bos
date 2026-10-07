<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', Employee::class)
            || $this->user()->hasRole('super-admin')
            || $this->user()->hasRole('hr');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'employee_number' => ['nullable', 'string', 'max:255'],
            'nik_ktp' => ['nullable', 'string', 'max:32'],
            'position' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'no_jmo' => ['nullable', 'string', 'max:50'],
            'no_kpj' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:25'],
            'photo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
            'join_date' => ['nullable', 'date'],
            'resign_date' => ['nullable', 'date'],
            'is_active' => ['nullable'],
            'gender' => ['nullable', 'in:'.implode(',', Employee::GENDERS)],
            'birth_place' => ['nullable', 'string', 'max:255'],
            'birth_date' => ['nullable', 'date'],
            'department' => ['nullable', 'string', 'max:50'],
            'employment_status' => ['nullable', 'string', 'max:50'],
            'contract_end_date' => ['nullable', 'date'],
            'salary' => ['nullable', 'numeric', 'min:0'],
            'user_id' => ['nullable', 'integer', 'exists:users,id'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
