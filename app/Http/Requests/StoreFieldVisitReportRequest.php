<?php

namespace App\Http\Requests;

use App\Models\FieldVisitReport;
use Illuminate\Foundation\Http\FormRequest;

class StoreFieldVisitReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', FieldVisitReport::class);
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'integer', 'exists:customers,id'],
            'location_label' => ['nullable', 'string', 'max:255', 'required_without:customer_id'],
            'purpose' => ['required', 'in:'.implode(',', FieldVisitReport::PURPOSES)],
            'visit_date' => ['required', 'date'],
            'notes' => ['nullable', 'string'],
            'result' => ['nullable', 'string'],
        ];
    }
}
