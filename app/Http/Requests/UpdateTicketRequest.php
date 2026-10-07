<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('ticket'));
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('customer_id')) {
            $customer = \App\Models\Customer::find($this->customer_id);
            if ($customer) {
                if (empty($this->title)) {
                    $this->merge([
                        'title' => $customer->company ?: $customer->name,
                    ]);
                }
                if (empty($this->customer_category)) {
                    $this->merge([
                        'customer_category' => $customer->category ?? 'HOME',
                    ]);
                }
            }
        }
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', Rule::in(Ticket::CATEGORIES)],
            'subcategory' => ['nullable', 'string', 'max:255'],
            'priority' => ['required', Rule::in(Ticket::PRIORITIES)],
            'team_id' => ['nullable', 'integer', 'exists:teams,id'],
            'device' => ['nullable', 'string', 'max:255'],
            'location' => ['nullable', 'string', 'max:255'],
            'service' => ['nullable', 'string', 'max:255'],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'customer_category' => ['nullable', 'string', Rule::in(['HOME', 'CORPORATE', 'RESELLER'])],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:20'],
        ];
    }
}
