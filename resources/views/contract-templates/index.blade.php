@extends('layouts.app')

@section('title', 'Redaksi PKWT / PKWTT')

@section('content')
    <h5 class="mb-3">Redaksi PKWT / PKWTT</h5>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Jenis Dokumen</th>
                        <th>Terakhir Diubah</th>
                        <th style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($templates as $template)
                        <tr>
                            <td>{{ $template->title }}</td>
                            <td class="small text-muted">
                                @if ($template->updatedBy)
                                    {{ $template->updatedBy->name }} — {{ $template->updated_at->format('d-m-Y H:i') }}
                                @else
                                    Belum pernah diubah (masih redaksi standar)
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('contract-templates.edit', $template->type) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
