<?php

namespace App\Http\Requests;

use App\Models\TroubleshootingSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTroubleshootingSessionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('ticket'));
    }

    public function rules(): array
    {
        return [
            'category' => ['required', Rule::in(TroubleshootingSession::CATEGORIES)],
            'observation' => ['required', 'string'],
            'hypothesis' => ['nullable', 'string'],
            'physical_checklist' => ['array'],
            'physical_checklist.*' => [Rule::in(TroubleshootingSession::PHYSICAL_CHECKLIST)],
            'logic_checklist' => ['array'],
            'logic_checklist.*' => [Rule::in(TroubleshootingSession::LOGIC_CHECKLIST)],
        ];
    }
}
