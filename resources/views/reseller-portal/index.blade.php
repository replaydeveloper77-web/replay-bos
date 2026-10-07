@extends('layouts.app')

@section('title', 'Portal Reseller')

@section('content')
    <div class="mb-3">
        <h5 class="mb-1">Selamat datang, {{ $reseller->name }}</h5>
        <p class="text-muted small mb-0">Daftar pelanggan Home yang terdaftar di bawah Reseller Anda.</p>
    </div>

    <div class="row g-3 mb-3">
        <div class="col-sm-6 col-lg-4">
            <div class="noc-stat-card bg-white p-3">
                <div class="text-muted small">Total Pelanggan Home</div>
                <div class="fs-3 fw-semibold">{{ $homeCustomers->count() }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="noc-stat-card bg-white p-3">
                <div class="text-muted small">Aktif</div>
                <div class="fs-3 fw-semibold text-success">{{ $homeCustomers->where('is_active', true)->count() }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-4">
            <div class="noc-stat-card bg-white p-3">
                <div class="text-muted small">Menunggu Aktivasi</div>
                <div class="fs-3 fw-semibold text-warning">{{ $homeCustomers->where('activation_status', 'PENDING')->count() }}</div>
            </div>
        </div>
    </div>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>WhatsApp</th>
                        <th>Lokasi</th>
                        <th class="text-center">Ticket</th>
                        <th>Status</th>
                        <th style="width: 90px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($homeCustomers as $home)
                        <tr>
                            <td>{{ $home->name }}</td>
                            <td>{{ $home->whatsapp ?? '-' }}</td>
                            <td>{{ $home->location ?? '-' }}</td>
                            <td class="text-center">{{ $home->tickets_count }}</td>
                            <td>
                                @if ($home->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                                @if ($home->activation_status === 'PENDING')
                                    <span class="badge bg-warning text-dark">Menunggu Aktivasi</span>
                                @endif
                            </td>
                            <td><a href="{{ route('reseller-portal.show', $home) }}" class="btn btn-sm btn-outline-secondary">Lihat</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-3">Belum ada pelanggan Home yang terdaftar di bawah Reseller Anda.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
