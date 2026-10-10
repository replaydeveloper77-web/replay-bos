<?php

namespace App\Http\Requests;

use App\Models\PackageChangeRequest;
use Illuminate\Foundation\Http\FormRequest;

class StorePackageChangeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', PackageChangeRequest::class);
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['required', 'integer', 'exists:customers,id'],
            'type' => ['required', 'in:'.implode(',', PackageChangeRequest::TYPES)],
            'target_package' => ['required', 'string', 'max:255'],
            'target_service_package_id' => ['nullable', 'integer', 'exists:service_packages,id'],
            'target_price' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'customer_id.required' => 'Pilih pelanggan yang akan diubah paketnya.',
            'customer_id.exists' => 'Pelanggan tidak valid.',
            'type.required' => 'Pilih jenis perubahan paket (Upgrade / Downgrade).',
            'type.in' => 'Jenis perubahan harus Upgrade atau Downgrade.',
            'target_package.required' => 'Ketik nama paket baru tujuan.',
            'target_package.max' => 'Nama paket baru maksimal 255 karakter.',
            'target_service_package_id.exists' => 'Paket katalog tidak valid.',
        ];
    }
}
