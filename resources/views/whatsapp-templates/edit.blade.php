@extends('layouts.app')

@section('title', 'Edit Template WhatsApp')

@section('content')
    <h5 class="mb-3">Edit Template — {{ $template->name }} <span class="badge bg-secondary">{{ $template->key }}</span></h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 720px;">
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('whatsapp-templates.update', $template) }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama Template</label>
                <input type="text" name="name" value="{{ old('name', $template->name) }}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Isi Pesan</label>
                <textarea name="body" class="form-control" rows="14" style="font-family: monospace;">{{ old('body', $template->body) }}</textarea>
                <div class="form-text">
                    Placeholder yang bisa dipakai: <code>@{{nama}}</code> <code>@{{layanan}}</code>
                    <code>@{{lokasi}}</code> <code>@{{ticket_id}}</code> <code>@{{status}}</code>
                    <code>@{{waktu_mulai}}</code> <code>@{{waktu_resolved}}</code>
                </div>
            </div>

            <div class="form-check mb-3">
                <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $template->is_active) ? 'checked' : '' }}>
                <label class="form-check-label" for="is_active">Aktif (muncul di pilihan template saat kirim WhatsApp)</label>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('whatsapp-templates.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
