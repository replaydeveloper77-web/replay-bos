<?php

namespace App\Http\Requests;

use App\Models\CoveragePoint;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCoveragePointRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', CoveragePoint::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'radius_km' => ['required', 'numeric', 'min:0.1', 'max:200'],
            'notes' => ['nullable', 'string', 'max:255'],
        ];
    }
}
