<?php

namespace App\Http\Requests;

use App\Models\Quotation;
use Illuminate\Foundation\Http\FormRequest;

class StoreQuotationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Quotation::class);
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('items') && is_array($this->items)) {
            $items = $this->items;
            foreach ($items as $k => $item) {
                if (isset($item['unit_price']) && is_string($item['unit_price'])) {
                    $cleaned = str_replace('.', '', $item['unit_price']);
                    $cleaned = str_replace(',', '.', $cleaned);
                    $items[$k]['unit_price'] = $cleaned;
                }
            }
            $this->merge(['items' => $items]);
        }
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'prospect_name' => ['required', 'string', 'max:255'],
            'prospect_company' => ['nullable', 'string', 'max:255'],
            'prospect_address' => ['nullable', 'string', 'max:255'],
            'prospect_phone' => ['nullable', 'string', 'max:30'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90', 'required_with:longitude'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180', 'required_with:latitude'],
            'valid_until' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.service_package_id' => ['nullable', 'integer', 'exists:service_packages,id'],
            'items.*.item_name' => ['required', 'string', 'max:255'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ];
    }
}
