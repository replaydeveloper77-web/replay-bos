@extends('layouts.app')

@section('title', 'Karyawan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Data Karyawan</h5>
        @can('create', \App\Models\Employee::class)
            <a href="{{ route('employees.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Karyawan
            </a>
        @endcan
    </div>

    <ul class="nav nav-pills mb-2">
        <li class="nav-item"><a class="nav-link {{ ! request('department') ? 'active' : '' }}" href="{{ route('employees.index', request()->except(['department', 'page'])) }}">Semua</a></li>
        @foreach (\App\Models\Employee::DEPARTMENTS as $dept)
            <li class="nav-item"><a class="nav-link {{ request('department') === $dept ? 'active' : '' }}" href="{{ route('employees.index', array_merge(request()->except('page'), ['department' => $dept])) }}">{{ ucfirst(strtolower($dept)) }}</a></li>
        @endforeach
    </ul>

    <form method="GET" class="mb-3">
        @if (request('department'))
            <input type="hidden" name="department" value="{{ request('department') }}">
        @endif
        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" style="max-width: 300px;" placeholder="Cari nama/no. induk/jabatan...">
    </form>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>No. Induk</th>
                        <th>Nama</th>
                        <th>Jabatan</th>
                        <th>Departemen</th>
                        <th>Status Kepegawaian</th>
                        <th>Tanggal Masuk</th>
                        <th>Status</th>
                        <th style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($employees as $employee)
                        <tr>
                            <td><code>{{ $employee->employee_number }}</code></td>
                            <td><a href="{{ route('employees.show', $employee) }}">{{ $employee->name }}</a></td>
                            <td>{{ $employee->position ?? '-' }}</td>
                            <td>{{ ucfirst(strtolower($employee->department)) }}</td>
                            <td>{{ ucfirst(strtolower($employee->employment_status)) }}</td>
                            <td>{{ $employee->join_date?->format('d/m/Y') ?? '-' }}</td>
                            <td>
                                @if ($employee->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Resign</span>
                                @endif
                            </td>
                            <td>
                                @can('update', \App\Models\Employee::class)
                                    <div class="d-grid gap-1">
                                        <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        @if ($employee->is_active)
                                            <form method="POST" action="{{ route('employees.disable', $employee) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Tandai karyawan ini resign/tidak aktif?')">Resign</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('employees.enable', $employee) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success w-100">Aktifkan</button>
                                            </form>
                                        @endif
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted text-center py-3">Belum ada data karyawan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $employees->links() }}
    </div>
@endsection
