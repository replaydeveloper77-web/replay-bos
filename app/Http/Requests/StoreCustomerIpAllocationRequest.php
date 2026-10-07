<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;

class StoreCustomerIpAllocationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', Customer::class);
    }

    public function rules(): array
    {
        return [
            'ip_address' => ['required', 'ip', 'unique:customer_ip_allocations,ip_address'],
            'label' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'ip_address.ip' => 'Format IP tidak valid.',
            'ip_address.unique' => 'IP ini sudah dialokasikan ke pelanggan lain.',
        ];
    }
}
