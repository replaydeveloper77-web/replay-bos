@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
    <h5 class="mb-3">Laporan Absensi</h5>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-3 mb-3">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="employee_id" class="form-select">
                    <option value="">- Semua Karyawan -</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" placeholder="Dari tanggal">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" placeholder="Sampai tanggal">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>Tanggal</th>
                        <th>Karyawan</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Catatan</th>
                        <th style="width: 70px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $row)
                        <tr>
                            <td>{{ $row->date->format('d-m-Y') }}</td>
                            <td>{{ $row->employee->name }}</td>
                            <td>{{ $row->check_in_at?->format('H:i') ?? '-' }}</td>
                            <td>{{ $row->check_out_at?->format('H:i') ?? '-' }}</td>
                            <td class="small text-muted">{{ \Illuminate\Support\Str::limit($row->notes, 40) ?: '-' }}</td>
                            <td>
                                @can('update', \App\Models\Attendance::class)
                                    <a href="{{ route('attendance.edit', $row) }}" class="btn btn-sm btn-outline-secondary">Koreksi</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-3">Belum ada data absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $attendances->withQueryString()->links() }}
    </div>
@endsection
