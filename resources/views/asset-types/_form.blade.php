@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Jenis Aset</label>
        <input type="text" name="name" value="{{ old('name', $assetType?->name) }}" class="form-control" required placeholder="mis. ONT Huawei HG8245F">
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Kategori</label>
        <select name="category" class="form-select" required>
            @foreach (\App\Models\AssetType::CATEGORIES as $cat)
                <option value="{{ $cat }}" @selected(old('category', $assetType?->category) === $cat)>
                    {{ $cat === 'CUSTOMER_PREMISES' ? 'Perangkat Pelanggan' : ($cat === 'NETWORK_INFRA' ? 'Infrastruktur Jaringan' : 'Lainnya') }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Satuan</label>
        <input type="text" name="unit" value="{{ old('unit', $assetType?->unit ?? 'pcs') }}" class="form-control">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Catatan (opsional)</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $assetType?->notes) }}</textarea>
</div>
