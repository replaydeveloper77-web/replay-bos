<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Asset::class);
    }

    public function rules(): array
    {
        return [
            'asset_type_id' => ['required', 'integer', 'exists:asset_types,id'],
            'serial_number' => ['nullable', 'string', 'max:255', 'unique:assets,serial_number'],
            'purchase_date' => ['nullable', 'date'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
