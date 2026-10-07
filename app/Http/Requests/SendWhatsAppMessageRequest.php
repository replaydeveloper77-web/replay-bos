<?php

namespace App\Http\Requests;

use App\Models\WhatsAppMessage;
use App\Models\WhatsAppTemplate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SendWhatsAppMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('whatsapp.send');
    }

    public function rules(): array
    {
        return [
            'recipient_type' => ['required', Rule::in(WhatsAppMessage::RECIPIENT_TYPES)],
            'recipient_name' => ['nullable', 'string', 'max:255'],
            'phone' => ['required_if:recipient_type,CUSTOM', 'nullable', 'string', 'max:20'],
            'template_key' => ['nullable', Rule::in(WhatsAppTemplate::KEYS)],
            'message' => ['required', 'string'],
        ];
    }
}
