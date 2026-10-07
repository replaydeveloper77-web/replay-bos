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
    <div class="col-md-8 mb-3">
        <label class="form-label">Nama Paket</label>
        <input type="text" name="name" value="{{ old('name', $package?->name) }}" class="form-control" required placeholder="mis. Internet Dedicated Bisnis">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Kecepatan (opsional)</label>
        <input type="text" name="speed_label" value="{{ old('speed_label', $package?->speed_label) }}" class="form-control" placeholder="mis. 50 Mbps">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Harga per Bulan (belum PPN)</label>
    <input type="number" step="0.01" min="0" name="price" value="{{ old('price', $package?->price) }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi (opsional)</label>
    <textarea name="description" class="form-control" rows="3">{{ old('description', $package?->description) }}</textarea>
</div>
