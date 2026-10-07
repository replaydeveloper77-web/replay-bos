<?php

namespace App\Http\Requests;

use App\Models\AssetType;
use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetTypeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', AssetType::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:'.implode(',', AssetType::CATEGORIES)],
            'unit' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ];
    }
}
