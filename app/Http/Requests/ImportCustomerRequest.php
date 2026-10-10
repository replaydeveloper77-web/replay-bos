<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class ImportCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Customer::class);
    }

    public function rules(): array
    {
        return [
            'file' => [
                'required',
                'file',
                'mimes:xlsx,xls,csv,txt',
                'max:10240', // 10MB max
            ],
            'default_category' => [
                'nullable',
                'string',
                'in:'.implode(',', Customer::CATEGORIES),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'file.required' => 'Silakan pilih file spreadsheet (Excel / CSV) terlebih dahulu.',
            'file.file' => 'File yang diunggah tidak valid.',
            'file.mimes' => 'Format file harus berupa Excel (.xlsx, .xls) atau CSV (.csv).',
            'file.max' => 'Ukuran file spreadsheet maksimal 10 MB.',
            'default_category.in' => 'Kategori default yang dipilih tidak valid.',
        ];
    }
}
