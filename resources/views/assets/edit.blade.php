@extends('layouts.app')

@section('title', 'Edit Aset')

@section('content')
    <h5 class="mb-3">Edit Aset — {{ $asset->asset_code }}</h5>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-4" style="max-width: 640px;">
        <div class="mb-3">
            <label class="form-label">Jenis Aset</label>
            <input type="text" class="form-control" value="{{ $asset->assetType->name }}" disabled>
            <div class="form-text">Jenis aset tidak bisa diubah setelah dibuat.</div>
        </div>
        <form method="POST" action="{{ route('assets.update', $asset) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Nomor Seri (opsional)</label>
                <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="form-control">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Pembelian (opsional)</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date?->format('Y-m-d')) }}" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Beli (opsional)</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $asset->price) }}" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Catatan (opsional)</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $asset->notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('assets.show', $asset) }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
