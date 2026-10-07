@extends('layouts.app')

@section('title', 'Jenis Aset')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Jenis Aset / Perangkat</h5>
        @can('create', \App\Models\AssetType::class)
            <a href="{{ route('asset-types.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Jenis Aset
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-center">Jumlah Unit</th>
                        <th>Status</th>
                        <th style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assetTypes as $assetType)
                        <tr>
                            <td>{{ $assetType->name }}</td>
                            <td>{{ $assetType->category === 'CUSTOMER_PREMISES' ? 'Perangkat Pelanggan' : ($assetType->category === 'NETWORK_INFRA' ? 'Infrastruktur Jaringan' : 'Lainnya') }}</td>
                            <td>{{ $assetType->unit }}</td>
                            <td class="text-center">{{ $assetType->assets_count }}</td>
                            <td>
                                @if ($assetType->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                @can('update', \App\Models\AssetType::class)
                                    <div class="d-grid gap-1">
                                        <a href="{{ route('asset-types.edit', $assetType) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                        @if ($assetType->is_active)
                                            <form method="POST" action="{{ route('asset-types.disable', $assetType) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Nonaktifkan jenis aset ini?')">Nonaktifkan</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('asset-types.enable', $assetType) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-success w-100">Aktifkan</button>
                                            </form>
                                        @endif
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-3">Belum ada jenis aset.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
