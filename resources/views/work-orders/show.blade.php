@extends('layouts.app')

@section('title', 'Detail Work Order — ' . $workOrder->work_order_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Work Order {{ $workOrder->work_order_number }}</h5>
        @can('update', $workOrder)
            <a href="{{ route('work-orders.edit', $workOrder) }}" class="btn btn-outline-secondary btn-sm">Edit Penugasan</a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row">
        <div class="col-md-7">
            <div class="noc-stat-card bg-white p-4 mb-3">
                <table class="table table-borderless mb-0">
                    <tr>
                        <th style="width: 200px;">Pelanggan</th>
                        <td><a href="{{ route('customers.show', $workOrder->customer) }}">{{ $workOrder->customer->name }}</a></td>
                    </tr>
                    <tr>
                        <th>Jenis Pekerjaan</th>
                        <td>{{ $workOrder->typeLabel() }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $workOrder->status === 'DONE' ? 'success' : ($workOrder->status === 'CANCELLED' ? 'danger' : ($workOrder->status === 'IN_PROGRESS' ? 'warning' : 'secondary')) }}">
                                {{ $workOrder->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Ditugaskan ke</th>
                        <td>
                            @if ($workOrder->assignedTo)
                                {{ $workOrder->assignedTo->name }}
                            @elseif ($workOrder->team)
                                Tim {{ $workOrder->team->name }}
                            @else
                                <span class="text-muted">Belum ditugaskan</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Tanggal Jadwal</th>
                        <td>{{ $workOrder->scheduled_date?->format('d-m-Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catatan Penugasan</th>
                        <td>{{ $workOrder->notes ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catatan Hasil Pengerjaan</th>
                        <td>{{ $workOrder->completion_notes ?? '-' }}</td>
                    </tr>
                    @if ($workOrder->status === 'DONE')
                        <tr>
                            <th>Diselesaikan Oleh</th>
                            <td>{{ $workOrder->completedBy->name ?? '-' }} — {{ $workOrder->completed_at?->format('d-m-Y H:i') }}</td>
                        </tr>
                    @endif
                    <tr>
                        <th>Dibuat Oleh</th>
                        <td>{{ $workOrder->createdBy->name }} — {{ $workOrder->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-5">
            @can('update', $workOrder)
                @if (! in_array($workOrder->status, ['DONE', 'CANCELLED'], true))
                    <div class="noc-stat-card bg-white p-4">
                        <h6 class="mb-3">Ubah Status Work Order</h6>
                        <form method="POST" action="{{ route('work-orders.change-status', $workOrder) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Status Baru</label>
                                <select name="status" id="status-select" class="form-select" required>
                                    @foreach (\App\Models\WorkOrder::STATUSES as $status)
                                        <option value="{{ $status }}" @selected($workOrder->status === $status)>{{ \App\Models\WorkOrder::statusLabelFor($status) }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3" id="completion-notes-field">
                                <label class="form-label">Catatan Hasil Pengerjaan (wajib jika Selesai)</label>
                                <textarea name="completion_notes" class="form-control" rows="3">{{ old('completion_notes', $workOrder->completion_notes) }}</textarea>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Simpan Status</button>
                        </form>
                    </div>
                @endif
            @endcan
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const select = document.getElementById('status-select');
        const notesField = document.getElementById('completion-notes-field');

        function toggle() {
            notesField.style.display = select.value === 'DONE' ? '' : 'none';
        }

        select.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endpush
