<?php

namespace App\Http\Requests;

use App\Models\Asset;
use Illuminate\Foundation\Http\FormRequest;

class ChangeAssetStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', Asset::class);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:'.implode(',', Asset::STATUSES)],
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'coverage_point_id' => ['nullable', 'integer', 'exists:coverage_points,id'],
            'location_note' => ['nullable', 'string', 'max:255'],
        ];
    }
}
