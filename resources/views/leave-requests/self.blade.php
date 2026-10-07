@extends('layouts.app')

@section('title', 'Cuti / Izin Saya')

@section('content')
    <h5 class="mb-3">Cuti / Izin Saya — {{ $employee->name }}</h5>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            @if ($employee->employment_status === 'TETAP')
                <div class="noc-stat-card bg-white p-4 mb-3">
                    <h6 class="mb-2">Kuota Cuti Tahunan {{ now()->year }}</h6>
                    <p class="mb-0">Kuota: <strong>{{ $quota['quota'] }} hari</strong> — Terpakai: <strong>{{ $quota['used'] }} hari</strong> — Sisa: <strong class="text-success">{{ $quota['remaining'] }} hari</strong></p>
                    <p class="small text-muted mb-0">Sisa kuota tahun ini tidak terbawa ke tahun depan.</p>
                </div>
            @endif

            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Ajukan Cuti / Izin Baru</h6>
                <form method="POST" action="{{ route('leave-requests.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Jenis</label>
                        <select name="type" class="form-select" required>
                            @foreach (\App\Models\LeaveRequest::TYPES as $type)
                                <option value="{{ $type }}" @selected(old('type') === $type)>{{ \App\Models\LeaveRequest::typeLabelFor($type) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Sampai Tanggal</label>
                            <input type="date" name="end_date" value="{{ old('end_date') }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alasan</label>
                        <textarea name="reason" class="form-control" rows="3">{{ old('reason') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Ajukan</button>
                </form>
            </div>
        </div>

        <div class="col-md-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Riwayat Pengajuan</h6>
                @if ($requests->isEmpty())
                    <p class="text-muted small mb-0">Belum pernah mengajukan cuti/izin.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Tanggal</th>
                                    <th>Hari</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($requests as $lr)
                                    <tr>
                                        <td>{{ $lr->typeLabel() }}</td>
                                        <td>{{ $lr->start_date->format('d/m/Y') }} - {{ $lr->end_date->format('d/m/Y') }}</td>
                                        <td>{{ $lr->days }}</td>
                                        <td>
                                            <span class="badge bg-{{ $lr->status === 'APPROVED' ? 'success' : ($lr->status === 'REJECTED' ? 'danger' : ($lr->status === 'CANCELLED' ? 'secondary' : 'warning text-dark')) }}">
                                                {{ $lr->statusLabel() }}
                                            </span>
                                            @if ($lr->status === 'REJECTED' && $lr->rejection_reason)
                                                <div class="small text-muted">{{ $lr->rejection_reason }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            @if ($lr->status === 'PENDING')
                                                <form method="POST" action="{{ route('leave-requests.cancel', $lr) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-danger" onclick="return confirm('Batalkan pengajuan ini?')">Batalkan</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
