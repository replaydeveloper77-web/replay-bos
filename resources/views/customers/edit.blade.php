@extends('layouts.app')

@section('title', 'Edit Pelanggan')

@section('content')
    <h5 class="mb-3">Edit Pelanggan — {{ $customer->name }}</h5>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="max-width: 640px;">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf
            @method('PUT')
            @include('customers._form', ['customer' => $customer])
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>

    <div class="noc-stat-card bg-white p-4 mt-3">
        <h6 class="mb-3">Alokasi IP</h6>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($customer->ipAllocations->isEmpty())
            <p class="text-muted small mb-3">Belum ada IP yang dialokasikan ke pelanggan ini.</p>
        @else
            <table class="table table-sm align-middle mb-3">
                <thead>
                    <tr>
                        <th>IP Address</th>
                        <th>Label</th>
                        <th>Dialokasikan oleh</th>
                        <th>Tanggal</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customer->ipAllocations as $allocation)
                        <tr id="ip-view-{{ $allocation->id }}">
                            <td><code>{{ $allocation->ip_address }}</code></td>
                            <td>{{ $allocation->label ?? '-' }}</td>
                            <td>{{ $allocation->createdBy?->username }}</td>
                            <td>{{ $allocation->created_at->format('d/m/Y') }}</td>
                            <td class="text-end">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="toggleIpEdit({{ $allocation->id }})">Edit</button>
                                <form method="POST" action="{{ route('customers.ip-allocations.destroy', [$customer, $allocation]) }}" class="d-inline" onsubmit="return confirm('Hapus alokasi IP ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        <tr id="ip-edit-{{ $allocation->id }}" style="display:none;">
                            <td colspan="5">
                                <form method="POST" action="{{ route('customers.ip-allocations.update', [$customer, $allocation]) }}" class="d-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="text" name="ip_address" value="{{ $allocation->ip_address }}" class="form-control form-control-sm" required style="max-width: 220px;">
                                    <input type="text" name="label" value="{{ $allocation->label }}" class="form-control form-control-sm" placeholder="Label (opsional)">
                                    <button type="submit" class="btn btn-sm btn-primary text-nowrap">Simpan</button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary text-nowrap" onclick="toggleIpEdit({{ $allocation->id }})">Batal</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        <form method="POST" action="{{ route('customers.ip-allocations.store', $customer) }}" class="d-flex gap-2">
            @csrf
            <input type="text" name="ip_address" class="form-control form-control-sm" placeholder="mis. 103.191.218.50" required style="max-width: 220px;">
            <input type="text" name="label" class="form-control form-control-sm" placeholder="Label (opsional, mis. IP Utama)">
            <button type="submit" class="btn btn-sm btn-primary text-nowrap">Tambah IP</button>
        </form>
    </div>

    <div class="noc-stat-card bg-white p-4 mt-3">
        <h6 class="mb-3">Riwayat Ticket ({{ $tickets->count() }})</h6>
        @if ($tickets->isEmpty())
            <p class="text-muted small mb-0">Pelanggan ini belum pernah punya ticket.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Judul</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>PIC</th>
                            <th>Dibuat</th>
                            <th>Hasil Penanganan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td><a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                                <td>{{ $ticket->title }}</td>
                                <td>@include('tickets._priority_badge')</td>
                                <td>@include('tickets._status_badge')</td>
                                <td>{{ $ticket->pic?->name ?? '-' }}</td>
                                <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($ticket->resolution_note, 60) ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @push('scripts')
    <script>
        function toggleIpEdit(id) {
            var viewRow = document.getElementById('ip-view-' + id);
            var editRow = document.getElementById('ip-edit-' + id);
            var editing = editRow.style.display !== 'none';
            editRow.style.display = editing ? 'none' : 'table-row';
            viewRow.style.display = editing ? 'table-row' : 'none';
        }
    </script>
    @endpush
@endsection
