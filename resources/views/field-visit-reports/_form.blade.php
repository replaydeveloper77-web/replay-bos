@php $report = $fieldVisitReport ?? null; @endphp

<div class="mb-3">
    <label class="form-label">Pelanggan (opsional)</label>
    <select name="customer_id" id="customer-select" class="form-select">
        <option value="">- Bukan lokasi pelanggan -</option>
        @foreach ($customers as $customer)
            <option value="{{ $customer->id }}" @selected(old('customer_id', $report?->customer_id) == $customer->id)>{{ $customer->name }}</option>
        @endforeach
    </select>
</div>

<div class="mb-3" id="location-label-field">
    <label class="form-label">Nama/Alamat Lokasi (wajib jika bukan pelanggan)</label>
    <input type="text" name="location_label" value="{{ old('location_label', $report?->location_label) }}" class="form-control" placeholder="mis. Calon lokasi tower RT03/RW02 Desa Cipari">
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Tujuan Kunjungan</label>
        <select name="purpose" class="form-select" required>
            @foreach (\App\Models\FieldVisitReport::PURPOSES as $purpose)
                <option value="{{ $purpose }}" @selected(old('purpose', $report?->purpose ?? 'KUNJUNGAN_RUTIN') === $purpose)>{{ \App\Models\FieldVisitReport::purposeLabelFor($purpose) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Tanggal Kunjungan</label>
        <input type="date" name="visit_date" value="{{ old('visit_date', $report?->visit_date?->format('Y-m-d') ?? now()->toDateString()) }}" class="form-control" required>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Catatan Kunjungan</label>
    <textarea name="notes" class="form-control" rows="3" placeholder="Apa yang dikerjakan/diamati selama kunjungan">{{ old('notes', $report?->notes) }}</textarea>
</div>

<div class="mb-3">
    <label class="form-label">Hasil / Kesimpulan</label>
    <textarea name="result" class="form-control" rows="2">{{ old('result', $report?->result) }}</textarea>
</div>

@push('scripts')
<script>
    (function () {
        const select = document.getElementById('customer-select');
        const locationField = document.getElementById('location-label-field');

        function toggle() {
            locationField.style.display = select.value ? 'none' : '';
        }

        select.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endpush
