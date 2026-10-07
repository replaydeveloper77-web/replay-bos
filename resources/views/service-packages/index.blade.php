@extends('layouts.app')

@section('title', 'Paket Layanan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Paket Layanan &amp; Harga</h5>
        @can('create', \App\Models\ServicePackage::class)
            <a href="{{ route('service-packages.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Paket
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>Nama Paket</th>
                        <th>Kecepatan</th>
                        <th>Harga/Bulan (belum PPN)</th>
                        <th>Status</th>
                        <th style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($packages as $package)
                        <tr>
                            <td>{{ $package->name }}</td>
                            <td>{{ $package->speed_label ?? '-' }}</td>
                            <td>Rp {{ number_format($package->price, 0, ',', '.') }}</td>
                            <td>
                                @if ($package->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-grid gap-1">
                                    <a href="{{ route('service-packages.edit', $package) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    @if ($package->is_active)
                                        <form method="POST" action="{{ route('service-packages.disable', $package) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Nonaktifkan paket ini?')">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('service-packages.enable', $package) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success w-100">Aktifkan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center py-3">Belum ada paket layanan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
