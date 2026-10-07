<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Models\TicketTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketTitleRequest extends FormRequest
{
    public function authorize(): bool
    {
        $ticketTitle = $this->route('ticketTitle') ?? $this->route('ticket_title');

        return $this->user()->can('update', $ticketTitle ?? TicketTitle::class) || $this->user()->hasRole('super-admin');
    }

    public function rules(): array
    {
        $ticketTitle = $this->route('ticketTitle') ?? $this->route('ticket_title');
        $id = $ticketTitle instanceof TicketTitle ? $ticketTitle->id : $ticketTitle;

        return [
            'name' => ['required', 'string', 'max:255', Rule::unique('ticket_titles', 'name')->ignore($id)],
            'category' => ['required', Rule::in(Ticket::CATEGORIES)],
            'priority' => ['required', Rule::in(Ticket::PRIORITIES)],
            'default_description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
