@extends('layouts.app')

@section('title', 'Detail Aset — ' . $asset->asset_code)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Aset {{ $asset->asset_code }}</h5>
        @can('update', $asset)
            <a href="{{ route('assets.edit', $asset) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
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
                        <th style="width: 200px;">Kode Aset</th>
                        <td>{{ $asset->asset_code }}</td>
                    </tr>
                    <tr>
                        <th>Jenis Aset</th>
                        <td>{{ $asset->assetType->name }}</td>
                    </tr>
                    <tr>
                        <th>Nomor Seri</th>
                        <td>{{ $asset->serial_number ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <span class="badge bg-{{ $asset->status === 'INSTALLED' ? 'success' : ($asset->status === 'DAMAGED' ? 'danger' : ($asset->status === 'IN_STOCK' ? 'secondary' : 'warning')) }}">
                                {{ $asset->statusLabel() }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Terpasang di Pelanggan</th>
                        <td>
                            @if ($asset->customer)
                                <a href="{{ route('customers.show', $asset->customer) }}">{{ $asset->customer->name }}</a>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Terpasang di Titik Coverage</th>
                        <td>{{ $asset->coveragePoint->name ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catatan Lokasi</th>
                        <td>{{ $asset->location_note ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Pembelian</th>
                        <td>{{ $asset->purchase_date?->format('d-m-Y') ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Harga Beli</th>
                        <td>{{ $asset->price ? 'Rp ' . number_format($asset->price, 0, ',', '.') : '-' }}</td>
                    </tr>
                    <tr>
                        <th>Catatan</th>
                        <td>{{ $asset->notes ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Dicatat Oleh</th>
                        <td>{{ $asset->createdBy->name }} — {{ $asset->created_at->format('d-m-Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="col-md-5">
            @can('update', $asset)
                <div class="noc-stat-card bg-white p-4">
                    <h6 class="mb-3">Ubah Status Aset</h6>
                    <form method="POST" action="{{ route('assets.change-status', $asset) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Status Baru</label>
                            <select name="status" id="status-select" class="form-select" required>
                                @foreach (\App\Models\Asset::STATUSES as $status)
                                    <option value="{{ $status }}" @selected($asset->status === $status)>{{ \App\Models\Asset::labelFor($status) }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="customer-field">
                            <label class="form-label">Pelanggan (jika status Terpasang)</label>
                            <select name="customer_id" class="form-select">
                                <option value="">- Pilih Pelanggan -</option>
                                @foreach ($customers as $customer)
                                    <option value="{{ $customer->id }}" @selected($asset->customer_id === $customer->id)>{{ $customer->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3" id="coverage-field">
                            <label class="form-label">Titik Coverage (jika status Terpasang, alternatif dari pelanggan)</label>
                            <select name="coverage_point_id" class="form-select">
                                <option value="">- Pilih Titik Coverage -</option>
                                @foreach ($coveragePoints as $point)
                                    <option value="{{ $point->id }}" @selected($asset->coverage_point_id === $point->id)>{{ $point->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan Lokasi (opsional)</label>
                            <input type="text" name="location_note" value="{{ old('location_note', $asset->location_note) }}" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Simpan Status</button>
                    </form>
                </div>
            @endcan
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const select = document.getElementById('status-select');
        const customerField = document.getElementById('customer-field');
        const coverageField = document.getElementById('coverage-field');

        function toggle() {
            const isInstalled = select.value === 'INSTALLED';
            customerField.style.display = isInstalled ? '' : 'none';
            coverageField.style.display = isInstalled ? '' : 'none';
        }

        select.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endpush
