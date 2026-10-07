<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use App\Models\TicketTitle;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreTicketTitleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', TicketTitle::class) || $this->user()->hasRole('super-admin');
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255', 'unique:ticket_titles,name'],
            'category' => ['required', Rule::in(Ticket::CATEGORIES)],
            'priority' => ['required', Rule::in(Ticket::PRIORITIES)],
            'default_description' => ['nullable', 'string'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
