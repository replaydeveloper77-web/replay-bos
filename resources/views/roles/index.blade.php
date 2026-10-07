@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
    <h5 class="mb-3">Roles &amp; Permissions</h5>

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
                        <th>Role</th>
                        <th>Deskripsi</th>
                        <th>Jumlah User</th>
                        <th>Jumlah Permission</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($roles as $role)
                        <tr>
                            <td>{{ $role->name }}</td>
                            <td>{{ $role->description }}</td>
                            <td>{{ $role->users_count }}</td>
                            <td>{{ $role->permissions_count }}</td>
                            <td class="text-end">
                                @can('manage', \App\Models\Role::class)
                                    <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary">Kelola Permission</a>
                                @endcan
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
