@extends('layouts.app')

@section('title', 'Tim')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Tim</h5>
        @can('create', \App\Models\Team::class)
            <a href="{{ route('teams.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Tim
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nama Tim</th>
                        <th>Deskripsi</th>
                        <th>Jumlah Anggota</th>
                        <th>Jumlah Ticket</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($teams as $team)
                        <tr>
                            <td>{{ $team->name }}</td>
                            <td>{{ $team->description ?? '-' }}</td>
                            <td>{{ $team->members_count }}</td>
                            <td>{{ $team->tickets_count }}</td>
                            <td>
                                @if ($team->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @can('update', \App\Models\Team::class)
                                    <a href="{{ route('teams.edit', $team) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    @if ($team->is_active)
                                        <form method="POST" action="{{ route('teams.disable', $team) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Nonaktifkan tim ini?')">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('teams.enable', $team) }}" class="d-inline">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success">Aktifkan</button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-3">Belum ada tim.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
