<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CloseTroubleshootingSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('session')->ticket);
    }

    public function rules(): array
    {
        return [
            'conclusion' => ['required', 'string'],
        ];
    }
}
