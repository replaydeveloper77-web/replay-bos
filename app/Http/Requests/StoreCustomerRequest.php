<?php

namespace App\Http\Requests;

use App\Models\Customer;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Customer::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:'.implode(',', Customer::CATEGORIES)],
            'reseller_id' => [
                'nullable',
                'integer',
                Rule::exists('customers', 'id')->where('category', 'RESELLER'),
                function ($attribute, $value, $fail) {
                    if ($value && $this->input('category') !== 'HOME') {
                        $fail('Reseller cuma bisa dipilih untuk kategori Home.');
                    }
                },
            ],
            'company' => ['nullable', 'string', 'max:255'],
            'pic_name' => ['nullable', 'string', 'max:255'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'service' => ['nullable', 'string', 'max:255'],
            'service_number' => ['nullable', 'string', 'max:255'],
            'service_package_id' => ['nullable', 'integer', 'exists:service_packages,id'],
            'subscription_price' => ['nullable', 'numeric', 'min:0'],
            'billing_due_day' => ['nullable', 'integer', 'min:1', 'max:28'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
