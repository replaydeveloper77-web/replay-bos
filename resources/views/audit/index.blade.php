@extends('layouts.app')

@section('title', 'Audit Trail')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Audit Trail</h5>
        <form method="GET" class="d-flex gap-2">
            <select name="module" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Module</option>
                @foreach ($modules as $module)
                    <option value="{{ $module }}" {{ request('module') === $module ? 'selected' : '' }}>{{ $module }}</option>
                @endforeach
            </select>
        </form>
    </div>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>User</th>
                        <th>IP</th>
                        <th>Module</th>
                        <th>Action</th>
                        <th>Target</th>
                        <th>Result</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $log->username ?? '-' }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td><span class="badge bg-secondary">{{ $log->module }}</span></td>
                            <td>{{ $log->action }}</td>
                            <td>{{ $log->target }}{{ $log->target_id ? " #{$log->target_id}" : '' }}</td>
                            <td>
                                <span class="badge {{ $log->result === 'SUCCESS' ? 'bg-success' : 'bg-danger' }}">{{ $log->result }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-3">Belum ada audit log.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $logs->links() }}
    </div>
@endsection
