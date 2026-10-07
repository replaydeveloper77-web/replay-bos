<?php

namespace App\Http\Requests;

use App\Models\WorkOrder;
use Illuminate\Foundation\Http\FormRequest;

class ChangeWorkOrderStatusRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', WorkOrder::class);
    }

    public function rules(): array
    {
        return [
            'status' => ['required', 'in:'.implode(',', WorkOrder::STATUSES)],
            'completion_notes' => ['nullable', 'string'],
        ];
    }
}
