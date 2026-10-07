@extends('layouts.app')

@section('title', 'Aset / Inventaris')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Aset / Inventaris</h5>
        @can('create', \App\Models\Asset::class)
            <a href="{{ route('assets.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Catat Aset Baru
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-3 mb-3">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari kode aset / nomor seri...">
            </div>
            <div class="col-md-3">
                <select name="asset_type_id" class="form-select">
                    <option value="">- Semua Jenis Aset -</option>
                    @foreach ($assetTypes as $type)
                        <option value="{{ $type->id }}" @selected(request('asset_type_id') == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">- Semua Status -</option>
                    @foreach (\App\Models\Asset::STATUSES as $status)
                        <option value="{{ $status }}" @selected(request('status') === $status)>{{ \App\Models\Asset::labelFor($status) }}</option>
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
                        <th>Kode Aset</th>
                        <th>Jenis</th>
                        <th>Nomor Seri</th>
                        <th>Status</th>
                        <th>Lokasi / Pemilik</th>
                        <th style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($assets as $asset)
                        <tr>
                            <td>{{ $asset->asset_code }}</td>
                            <td>{{ $asset->assetType->name }}</td>
                            <td>{{ $asset->serial_number ?? '-' }}</td>
                            <td>
                                <span class="badge bg-{{ $asset->status === 'INSTALLED' ? 'success' : ($asset->status === 'DAMAGED' ? 'danger' : ($asset->status === 'IN_STOCK' ? 'secondary' : 'warning')) }}">
                                    {{ $asset->statusLabel() }}
                                </span>
                            </td>
                            <td>
                                @if ($asset->customer)
                                    Pelanggan: {{ $asset->customer->name }}
                                @elseif ($asset->coveragePoint)
                                    Titik: {{ $asset->coveragePoint->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('assets.show', $asset) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-3">Belum ada aset.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $assets->withQueryString()->links() }}
    </div>
@endsection
