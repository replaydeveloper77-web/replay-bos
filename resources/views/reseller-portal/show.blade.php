@extends('layouts.app')

@section('title', $customer->name)

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">{{ $customer->name }}</h5>
            @if ($customer->is_active)
                <span class="badge bg-success">Aktif</span>
            @else
                <span class="badge bg-danger">Nonaktif</span>
            @endif
            @if ($customer->activation_status === 'PENDING')
                <span class="badge bg-warning text-dark">Menunggu Aktivasi</span>
            @endif
        </div>
        <a href="{{ route('reseller-portal.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
    </div>

    <div class="noc-stat-card bg-white p-4 mb-3">
        <h6 class="mb-3">Informasi Pelanggan</h6>
        <div class="row small">
            <div class="col-6 mb-3">
                <div class="text-muted">No. WhatsApp</div>
                <div>{{ $customer->whatsapp ?? '-' }}</div>
            </div>
            <div class="col-6 mb-3">
                <div class="text-muted">Lokasi</div>
                <div>{{ $customer->location ?? '-' }}</div>
            </div>
            <div class="col-6 mb-3">
                <div class="text-muted">Layanan</div>
                <div>{{ $customer->service ?? '-' }}</div>
            </div>
        </div>
    </div>

    <div class="noc-stat-card bg-white p-4">
        <h6 class="mb-3">Riwayat Ticket Terbaru</h6>
        @if ($tickets->isEmpty())
            <p class="text-muted small mb-0">Belum ada ticket.</p>
        @else
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Ticket</th>
                        <th>Judul</th>
                        <th>Status</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($tickets as $ticket)
                        <tr>
                            <td>{{ $ticket->ticket_number }}</td>
                            <td>{{ $ticket->title }}</td>
                            <td>@include('tickets._status_badge')</td>
                            <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
