<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('settings.manage');
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'company_address' => ['nullable', 'string', 'max:500'],
            'company_phone' => ['nullable', 'string', 'max:50'],
            'company_email' => ['nullable', 'email', 'max:255'],
            'ppn_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'registration_fee' => ['required', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'bank_account_holder' => ['nullable', 'string', 'max:255'],
            'company_representative_name' => ['nullable', 'string', 'max:255'],
            'company_representative_title' => ['nullable', 'string', 'max:100'],
            'company_representative_ktp' => ['nullable', 'string', 'max:32'],
            'company_akta_notaris' => ['nullable', 'string', 'max:100'],
            'company_sk_menkumham' => ['nullable', 'string', 'max:255'],
            'company_business_field' => ['nullable', 'string', 'max:255'],
        ];
    }
}
