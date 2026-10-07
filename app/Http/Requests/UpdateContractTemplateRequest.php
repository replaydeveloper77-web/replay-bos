<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateContractTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('contract.template.manage');
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string'],
        ];
    }
}
