@extends('layouts.app')

@section('title', 'Work Order Instalasi')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Work Order Instalasi</h5>
        @can('create', \App\Models\WorkOrder::class)
            <a href="{{ route('work-orders.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Buat Work Order
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-3 mb-3">
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nomor WO / nama pelanggan...">
            </div>
            <div class="col-md-4">
                <select name="status" class="form-select">
                    <option value="">- Semua Status -</option>
                    @foreach (\App\Models\WorkOrder::STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ \App\Models\WorkOrder::statusLabelFor($status) }}</option>
                    @endforeach
                </select>
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
                        <th>No. Work Order</th>
                        <th>Pelanggan</th>
                        <th>Jenis</th>
                        <th>Jadwal</th>
                        <th>Ditugaskan ke</th>
                        <th>Status</th>
                        <th style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($workOrders as $wo)
                        <tr>
                            <td>{{ $wo->work_order_number }}</td>
                            <td>{{ $wo->customer->name }}</td>
                            <td>{{ $wo->typeLabel() }}</td>
                            <td>{{ $wo->scheduled_date?->format('d-m-Y') ?? '-' }}</td>
                            <td>
                                @if ($wo->assignedTo)
                                    {{ $wo->assignedTo->name }}
                                @elseif ($wo->team)
                                    Tim {{ $wo->team->name }}
                                @else
                                    <span class="text-muted">Belum ditugaskan</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $wo->status === 'DONE' ? 'success' : ($wo->status === 'CANCELLED' ? 'danger' : ($wo->status === 'IN_PROGRESS' ? 'warning' : 'secondary')) }}">
                                    {{ $wo->statusLabel() }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('work-orders.show', $wo) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-3">Belum ada work order.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $workOrders->withQueryString()->links() }}
    </div>
@endsection
