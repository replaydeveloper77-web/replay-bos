@extends('layouts.app')

@section('title', 'Template WhatsApp')

@section('content')
    <h5 class="mb-3">Template WhatsApp</h5>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Key</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Preview</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($templates as $template)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $template->key }}</span></td>
                            <td>{{ $template->name }}</td>
                            <td>
                                @if ($template->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ \Illuminate\Support\Str::limit(str_replace("\n", ' ', $template->body), 60) ?: '(kosong, diisi manual saat kirim)' }}</td>
                            <td class="text-end">
                                <a href="{{ route('whatsapp-templates.edit', $template) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
