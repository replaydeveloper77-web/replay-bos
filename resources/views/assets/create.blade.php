@extends('layouts.app')

@section('title', 'Catat Aset Baru')

@section('content')
    <h5 class="mb-3">Catat Aset Baru</h5>

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
        <form method="POST" action="{{ route('assets.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Jenis Aset</label>
                <select name="asset_type_id" class="form-select" required>
                    <option value="">- Pilih Jenis Aset -</option>
                    @foreach ($assetTypes as $type)
                        <option value="{{ $type->id }}" @selected(old('asset_type_id') == $type->id)>{{ $type->name }}</option>
                    @endforeach
                </select>
                @if ($assetTypes->isEmpty())
                    <div class="form-text text-danger">Belum ada Jenis Aset — <a href="{{ route('asset-types.create') }}">tambah dulu di sini</a>.</div>
                @endif
            </div>
            <div class="mb-3">
                <label class="form-label">Nomor Seri (opsional)</label>
                <input type="text" name="serial_number" value="{{ old('serial_number') }}" class="form-control">
                <div class="form-text">Kosongkan kalau perangkat tidak punya nomor seri yang jelas — tetap dapat kode aset internal otomatis.</div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal Pembelian (opsional)</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date') }}" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Harga Beli (opsional)</label>
                    <input type="number" step="0.01" min="0" name="price" value="{{ old('price') }}" class="form-control">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Catatan (opsional)</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('assets.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
