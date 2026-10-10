<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class StoreTicketProgressUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('ticket'));
    }

    public function rules(): array
    {
        return [
            'title' => ['nullable', 'string', 'max:255'],
            'note' => ['required', 'string'],
            'status' => ['nullable', 'string', 'in:'.implode(',', Ticket::STATUSES)],
            'photos' => ['nullable', 'array', 'max:5'],
            'photos.*' => ['file', 'mimes:jpeg,png,jpg,gif,webp', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'note.required' => 'Deskripsi progress perbaikan wajib diisi.',
            'photos.max' => 'Maksimal 5 foto per satu kali update.',
            'photos.*.mimes' => 'Dokumentasi harus berupa file gambar (JPG, PNG, WEBP, GIF).',
            'photos.*.max' => 'Ukuran setiap foto maksimal 10 MB.',
        ];
    }
}
