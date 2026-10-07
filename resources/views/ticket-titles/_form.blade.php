@if (isset($errors) && $errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-3">
    <label class="form-label fw-medium">Nama Judul Tiket <span class="text-danger">*</span></label>
    <input type="text" name="name" value="{{ old('name', $title?->name) }}" class="form-control" placeholder="Contoh: Link Down, Gangguan Bandwidth, dsb." required>
    <div class="form-text">Nama atau template judul tiket standar yang akan dipilih saat membuat tiket.</div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label fw-medium">Kategori Gangguan Terkait <span class="text-danger">*</span></label>
        <select name="category" class="form-select" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category }}" {{ old('category', $title?->category) === $category ? 'selected' : '' }}>
                    {{ \App\Models\Ticket::categoryLabel($category) }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Kategori ini otomatis dipilih saat judul ini digunakan.</div>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label fw-medium">Default Priority <span class="text-danger">*</span></label>
        <select name="priority" class="form-select" required>
            <option value="">-- Pilih Priority --</option>
            @foreach ($priorities as $priority)
                <option value="{{ $priority }}" {{ old('priority', $title?->priority ?? 'HIGH') === $priority ? 'selected' : '' }}>
                    {{ $priority }}
                </option>
            @endforeach
        </select>
        <div class="form-text">Prioritas standar untuk gangguan ini (Critical, High, Medium, Low).</div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label fw-medium">Template Deskripsi (Opsional)</label>
    <textarea name="default_description" class="form-control" rows="3" placeholder="Template catatan / deskripsi awal yang otomatis muncul...">{{ old('default_description', $title?->default_description) }}</textarea>
</div>

<div class="mb-3">
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active" {{ old('is_active', $title?->is_active ?? true) ? 'checked' : '' }}>
        <label class="form-check-label" for="is_active">Aktifkan judul tiket ini (muncul di pilihan buat tiket)</label>
    </div>
</div>
