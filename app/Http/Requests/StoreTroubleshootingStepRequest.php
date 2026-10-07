<?php

namespace App\Http\Requests;

use App\Models\TroubleshootingStep;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTroubleshootingStepRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('session')->ticket);
    }

    public function rules(): array
    {
        return [
            'step_type' => ['required', Rule::in(TroubleshootingStep::STEP_TYPES)],
            'description' => ['required', 'string'],
        ];
    }
}
