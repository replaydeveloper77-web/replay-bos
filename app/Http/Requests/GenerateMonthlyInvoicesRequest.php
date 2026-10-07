<?php

namespace App\Http\Requests;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;

class GenerateMonthlyInvoicesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('manage', Invoice::class);
    }

    public function rules(): array
    {
        return [
            'period' => ['required', 'date_format:Y-m'],
        ];
    }
}
