<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWhatsAppTemplateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('whatsapp.template.manage');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string'],
            'is_active' => ['boolean'],
        ];
    }
}
