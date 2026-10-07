<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ChangeTicketStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticket = $this->route('ticket');

        if ($this->input('status') === 'ESCALATED') {
            return $this->user()->can('escalate', $ticket);
        }

        if (in_array($this->input('status'), Ticket::CLOSED_STATUSES, true)) {
            return $this->user()->can('close', $ticket);
        }

        return $this->user()->can('update', $ticket);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', Rule::in(Ticket::STATUSES)],
            'note' => ['nullable', 'string'],
            'escalation_level' => ['required_if:status,ESCALATED', 'nullable', Rule::in(Ticket::ESCALATION_LEVELS)],
        ];
    }
}
