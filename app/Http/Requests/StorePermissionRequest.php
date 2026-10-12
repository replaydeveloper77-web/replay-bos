<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;

class StorePermissionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('createPermission', Role::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', 'unique:permissions,slug', 'regex:/^[a-zA-Z0-9_\.\-]+$/'],
            'module' => ['required', 'string', 'max:100'],
            'description' => ['nullable', 'string', 'max:1000'],
            'role_ids' => ['nullable', 'array'],
            'role_ids.*' => ['exists:roles,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama permission wajib diisi.',
            'slug.required' => 'Slug permission wajib diisi.',
            'slug.unique' => 'Slug permission ini sudah ada. Gunakan kode slug yang berbeda.',
            'slug.regex' => 'Format slug hanya boleh memuat huruf, angka, titik, strip (-), atau underscore (_).',
            'module.required' => 'Kategori modul wajib diisi.',
            'role_ids.*.exists' => 'Salah satu role yang dipilih tidak valid.',
        ];
    }
}
