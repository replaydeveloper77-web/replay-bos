@if (isset($errors) && $errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

{{-- Hidden Title Input: otomatis terisi dari nama perusahaan/client yang dipilih --}}
<input type="hidden" name="title" id="ticket-title-input" value="{{ old('title', $ticket?->title) }}">

{{-- 1. PILIH CLIENT (PELANGGAN) --}}
<div class="card border-0 bg-light p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center mb-2">
        <label class="form-label fw-bold mb-0 text-primary">
            <i class="bi bi-person-bounding-box me-1"></i> Nama Client (Pelanggan) <span class="text-danger">*</span>
        </label>
        <small><a href="{{ route('customers.create') }}" target="_blank">+ Tambah Pelanggan Baru</a></small>
    </div>

    <div class="mb-2">
        <div class="input-group input-group-sm">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" id="customer-search-filter" class="form-control" placeholder="Ketik untuk mencari nama client / perusahaan / no. layanan...">
        </div>
    </div>

    <div>
        <select name="customer_id" id="customer-select" class="form-select" required>
            <option value="">-- Pilih Nama Client dari Master Data Pelanggan --</option>
            @foreach ($customers as $customer)
                @php
                    $displayName = $customer->company ? "{$customer->company} ({$customer->name})" : $customer->name;
                @endphp
                <option value="{{ $customer->id }}"
                    data-name="{{ $customer->name }}"
                    data-company="{{ $customer->company }}"
                    data-category="{{ $customer->category }}"
                    {{ old('customer_id', $ticket?->customer_id) == $customer->id ? 'selected' : '' }}>
                    [{{ $customer->service_number ?? $customer->category }}] {{ $displayName }}
                </option>
            @endforeach
        </select>
        <div class="form-text small text-muted">
            Pilih client/pelanggan dari master data. Data kontak (WhatsApp) & detail layanan diambil otomatis dari Master Data Pelanggan.
        </div>
    </div>
</div>

{{-- 2. TIPE PELANGGAN, HANDLE BY, CATEGORY & PRIORITY --}}
<div class="card border-0 bg-light p-3 mb-3">
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-medium">Tipe Pelanggan <span class="text-danger">*</span></label>
            <select name="customer_category" id="customer-category-select" class="form-select" required>
                <option value="HOME" {{ old('customer_category', $ticket?->customer?->category ?? ($ticket?->customer_category ?? 'HOME')) === 'HOME' ? 'selected' : '' }}>Home (RH)</option>
                <option value="CORPORATE" {{ old('customer_category', $ticket?->customer?->category ?? ($ticket?->customer_category ?? 'CORPORATE')) === 'CORPORATE' ? 'selected' : '' }}>Corporate (CP)</option>
                <option value="RESELLER" {{ old('customer_category', $ticket?->customer?->category ?? ($ticket?->customer_category ?? 'RESELLER')) === 'RESELLER' ? 'selected' : '' }}>Reseller (RS)</option>
            </select>
            <div class="form-text small">Menentukan format nomor tiket (CP/RS/RH). Terisi otomatis saat memilih client.</div>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-medium">Handle By (Tim Penanganan) <span class="text-danger">*</span></label>
            <select name="team_id" id="ticket-team-select" class="form-select" required>
                <option value="">-- Pilih Handle By (NOC, Vendor, dll) --</option>
                @foreach ($teams as $team)
                    <option value="{{ $team->id }}" {{ old('team_id', $ticket?->team_id) == $team->id ? 'selected' : '' }}>
                        {{ $team->name }}
                    </option>
                @endforeach
            </select>
            <div class="form-text small">Tim yang menangani tiket ini (misal: NOC, Vendor, Tim Teknis).</div>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-medium">Category <span class="text-danger">*</span></label>
            <select name="category" id="ticket-category-select" class="form-select" required>
                <option value="">-- Pilih Category --</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" {{ old('category', $ticket?->category) === $category ? 'selected' : '' }}>
                        {{ \App\Models\Ticket::categoryLabel($category) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label fw-medium">Priority <span class="text-danger">*</span></label>
            <select name="priority" id="ticket-priority-select" class="form-select" required>
                <option value="">-- Pilih Priority --</option>
                @foreach ($priorities as $priority)
                    <option value="{{ $priority }}" {{ old('priority', $ticket?->priority) === $priority ? 'selected' : '' }}>
                        {{ $priority }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
</div>

{{-- 3. DESKRIPSI GANGGUAN --}}
<div class="card border-0 bg-light p-3 mb-3">
    <label class="form-label fw-medium">Deskripsi <span class="text-danger">*</span></label>
    <textarea name="description" id="ticket-description-textarea" class="form-control" rows="4" required placeholder="Jelaskan detail masalah / kendala...">{{ old('description', $ticket?->description) }}</textarea>

    {{-- Informasi opsional (Subcategory, Device, Lokasi) --}}
    <details class="mt-3">
        <summary class="small text-muted" style="cursor: pointer;">
            <i class="bi bi-sliders me-1"></i> Informasi Tambahan (Opsional: Device, Lokasi, Subcategory)
        </summary>
        <div class="row g-2 mt-2 pt-2 border-top">
            <div class="col-md-4">
                <label class="form-label small text-muted">Subcategory</label>
                <input type="text" name="subcategory" value="{{ old('subcategory', $ticket?->subcategory) }}" class="form-control form-control-sm" placeholder="Contoh: Router, Port, Backbone">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Device</label>
                <input type="text" name="device" value="{{ old('device', $ticket?->device) }}" class="form-control form-control-sm" placeholder="Contoh: CCR-1036, OLT-ZTE">
            </div>
            <div class="col-md-4">
                <label class="form-label small text-muted">Lokasi</label>
                <input type="text" name="location" value="{{ old('location', $ticket?->location) }}" class="form-control form-control-sm" placeholder="Contoh: POP Datacenter, Gedung A">
            </div>
        </div>
    </details>
</div>

<script>
    (function () {
        var select = document.getElementById('customer-select');
        var categoryField = document.getElementById('customer-category-select');
        var titleInput = document.getElementById('ticket-title-input');
        var searchFilter = document.getElementById('customer-search-filter');

        function updateSelectedCustomer() {
            if (!select) return;
            var selectedCustOpt = select.selectedIndex >= 0 ? select.options[select.selectedIndex] : null;
            if (selectedCustOpt && selectedCustOpt.value) {
                var comp = selectedCustOpt.getAttribute('data-company');
                var name = selectedCustOpt.getAttribute('data-name');
                var cat = selectedCustOpt.getAttribute('data-category');

                if (titleInput) {
                    titleInput.value = comp || name || '';
                }

                if (categoryField && cat) {
                    categoryField.value = cat;
                }
            }
        }

        if (select) {
            select.addEventListener('change', updateSelectedCustomer);
            if (select.value) {
                updateSelectedCustomer();
            }
        }

        // Live search filter on customer dropdown
        if (searchFilter && select) {
            searchFilter.addEventListener('input', function () {
                var term = this.value.toLowerCase().trim();
                var options = select.options;
                var firstVisible = null;
                for (var i = 1; i < options.length; i++) {
                    var text = options[i].text.toLowerCase();
                    var match = term === '' || text.includes(term);
                    options[i].hidden = !match;
                    if (match && !firstVisible) {
                        firstVisible = options[i];
                    }
                }
            });
        }
    })();
</script>
