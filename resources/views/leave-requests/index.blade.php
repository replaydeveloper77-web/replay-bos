@extends('layouts.app')

@section('title', 'Pengajuan Cuti / Izin')

@section('content')
    <h5 class="mb-3">Pengajuan Cuti / Izin</h5>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-3 mb-3">
        <form method="GET" class="row g-2">
            <div class="col-md-5">
                <select name="employee_id" class="form-select">
                    <option value="">- Semua Karyawan -</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">- Semua Status -</option>
                    @foreach (\App\Models\LeaveRequest::STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ \App\Models\LeaveRequest::statusLabelFor($status) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Karyawan</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Hari</th>
                        <th>Alasan</th>
                        <th>Status</th>
                        <th style="width: 160px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($requests as $lr)
                        <tr>
                            <td>{{ $lr->employee->name }}</td>
                            <td>{{ $lr->typeLabel() }}</td>
                            <td class="text-nowrap">{{ $lr->start_date->format('d/m/Y') }} - {{ $lr->end_date->format('d/m/Y') }}</td>
                            <td>{{ $lr->days }}</td>
                            <td class="small text-muted">{{ \Illuminate\Support\Str::limit($lr->reason, 40) ?: '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $lr->status === 'APPROVED' ? 'success' : ($lr->status === 'REJECTED' ? 'danger' : ($lr->status === 'CANCELLED' ? 'secondary' : 'warning text-dark')) }}">
                                    {{ $lr->statusLabel() }}
                                </span>
                            </td>
                            <td>
                                @can('update', \App\Models\LeaveRequest::class)
                                    @if ($lr->status === 'PENDING')
                                        <div class="d-grid gap-1">
                                            <form method="POST" action="{{ route('leave-requests.approve', $lr) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-success w-100">Setujui</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-danger w-100" onclick="document.getElementById('reject-row-{{ $lr->id }}').classList.toggle('d-none')">Tolak</button>
                                        </div>
                                    @elseif ($lr->status === 'APPROVED')
                                        <form method="POST" action="{{ route('leave-requests.cancel', $lr) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-secondary" onclick="return confirm('Batalkan pengajuan yang sudah disetujui ini?')">Batalkan</button>
                                        </form>
                                    @endif
                                @endcan
                            </td>
                        </tr>
                        @can('update', \App\Models\LeaveRequest::class)
                            @if ($lr->status === 'PENDING')
                                <tr id="reject-row-{{ $lr->id }}" class="d-none">
                                    <td colspan="7">
                                        <form method="POST" action="{{ route('leave-requests.reject', $lr) }}" class="d-flex gap-2">
                                            @csrf
                                            <input type="text" name="rejection_reason" class="form-control form-control-sm" placeholder="Alasan penolakan (wajib)" required>
                                            <button class="btn btn-sm btn-danger">Kirim Penolakan</button>
                                        </form>
                                    </td>
                                </tr>
                            @endif
                        @endcan
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-3">Belum ada pengajuan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $requests->withQueryString()->links() }}
    </div>
@endsection
