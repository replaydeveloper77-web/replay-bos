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
        <label class="form-label">Nama Pelanggan</label>
        <input type="text" name="name" value="{{ old('name', $customer?->name) }}" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Perusahaan (opsional)</label>
        <input type="text" name="company" value="{{ old('company', $customer?->company) }}" class="form-control">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Kategori Pelanggan</label>
        <select name="category" id="category-select" class="form-select" required>
            @foreach (\App\Models\Customer::CATEGORIES as $cat)
                <option value="{{ $cat }}" @selected(old('category', $customer?->category) === $cat)>{{ ucfirst(strtolower($cat)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-6 mb-3" id="reseller-field" style="display: none;">
        <label class="form-label">Reseller Induk (opsional)</label>
        <select name="reseller_id" class="form-select">
            <option value="">- Langsung ke Replay (tanpa reseller) -</option>
            @foreach ($resellers ?? [] as $reseller)
                <option value="{{ $reseller->id }}" @selected(old('reseller_id', $customer?->reseller_id) == $reseller->id)>{{ $reseller->name }} @if($reseller->company) ({{ $reseller->company }}) @endif</option>
            @endforeach
        </select>
        <div class="form-text">Isi kalau pelanggan Home ini didaftarkan lewat Reseller tertentu.</div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            var categorySelect = document.getElementById('category-select');
            var resellerField = document.getElementById('reseller-field');
            var serviceNumberInput = document.getElementById('service-number-input');

            function updateCategoryUI() {
                var cat = categorySelect.value;
                resellerField.style.display = cat === 'HOME' ? '' : 'none';
                if (serviceNumberInput) {
                    var example = cat === 'RESELLER' ? '9202100001' : (cat === 'CORPORATE' ? '9202200001' : '9202300001');
                    serviceNumberInput.placeholder = 'Kosongkan untuk dibuat otomatis (' + example + ')';
                }
            }

            categorySelect.addEventListener('change', updateCategoryUI);
            updateCategoryUI();
        })();
    </script>
@endpush

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nama PIC (opsional)</label>
        <input type="text" name="pic_name" value="{{ old('pic_name', $customer?->pic_name) }}" class="form-control">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">No. WhatsApp (opsional)</label>
        <input type="text" name="whatsapp" value="{{ old('whatsapp', $customer?->whatsapp) }}" class="form-control" placeholder="08xxxxxxxxxx">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Email (opsional)</label>
        <input type="email" name="email" value="{{ old('email', $customer?->email) }}" class="form-control">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Lokasi (opsional)</label>
        <input type="text" name="location" value="{{ old('location', $customer?->location) }}" class="form-control">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Layanan (opsional)</label>
        <input type="text" name="service" value="{{ old('service', $customer?->service) }}" class="form-control" placeholder="mis. Internet Dedicated 50 Mbps">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Nomor Layanan (opsional)</label>
        <input type="text" name="service_number" id="service-number-input" value="{{ old('service_number', $customer?->service_number) }}" class="form-control" placeholder="Kosongkan untuk dibuat otomatis (misal: 9202100001, 9202200001, 9202300001)">
        <div class="form-text">Kosongkan kalau tidak punya nomor khusus — sistem buatkan otomatis (920210000[Urut] untuk Reseller, 920220000[Urut] untuk Corporate, 920230000[Urut] untuk Home).</div>
    </div>
</div>

<hr class="my-3">
<h6 class="mb-3">Langganan &amp; Penagihan</h6>
<div class="row">
    <div class="col-md-5 mb-3">
        <label class="form-label">Paket Langganan (opsional)</label>
        <select name="service_package_id" class="form-select">
            <option value="">- Tidak dikaitkan ke katalog paket -</option>
            @foreach ($packages ?? [] as $package)
                <option value="{{ $package->id }}" @selected(old('service_package_id', $customer?->service_package_id) == $package->id)>{{ $package->name }} @if($package->speed_label) ({{ $package->speed_label }}) @endif</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Harga Langganan/Bulan (opsional)</label>
        <input type="number" step="0.01" min="0" name="subscription_price" value="{{ old('subscription_price', $customer?->subscription_price) }}" class="form-control" placeholder="belum PPN">
        <div class="form-text">Isi kalau pelanggan ini mau ditagih otomatis tiap bulan oleh Finance. Kosongkan kalau tidak berlangganan bulanan.</div>
    </div>
    <div class="col-md-3 mb-3">
        <label class="form-label">Tanggal Jatuh Tempo</label>
        <input type="number" min="1" max="28" name="billing_due_day" value="{{ old('billing_due_day', $customer?->billing_due_day ?? 10) }}" class="form-control">
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Catatan (opsional)</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $customer?->notes) }}</textarea>
</div>
