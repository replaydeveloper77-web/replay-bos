@extends('layouts.app')

@section('title', $quotation ? 'Edit Surat Penawaran' : 'Buat Surat Penawaran')

@section('content')
    <h5 class="mb-3">{{ $quotation ? 'Edit Surat Penawaran' : 'Buat Surat Penawaran' }}</h5>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ $quotation ? route('quotations.update', $quotation) : route('quotations.store') }}">
        @csrf
        @if ($quotation) @method('PUT') @endif

        <div class="row g-3">
            <div class="col-lg-6">
                <div class="noc-stat-card bg-white p-4 mb-3">
                    <h6 class="mb-3">Data Prospek</h6>

                    <div class="mb-3">
                        <label class="form-label">Hubungkan ke Pelanggan terdaftar (opsional)</label>
                        <select name="customer_id" class="form-select">
                            <option value="">- Prospek baru / tidak terdaftar -</option>
                            @foreach ($customers as $c)
                                <option value="{{ $c->id }}" @selected(old('customer_id', $quotation?->customer_id) == $c->id)>{{ $c->name }} @if($c->company) ({{ $c->company }}) @endif</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Nama Prospek/PIC</label>
                        <input type="text" name="prospect_name" value="{{ old('prospect_name', $quotation?->prospect_name) }}" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Perusahaan (opsional)</label>
                        <input type="text" name="prospect_company" value="{{ old('prospect_company', $quotation?->prospect_company) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input type="text" name="prospect_address" value="{{ old('prospect_address', $quotation?->prospect_address) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. WhatsApp (opsional)</label>
                        <input type="text" name="prospect_phone" value="{{ old('prospect_phone', $quotation?->prospect_phone) }}" class="form-control" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Berlaku Sampai</label>
                        <input type="date" name="valid_until" value="{{ old('valid_until', $quotation?->valid_until?->format('Y-m-d') ?? now()->addDays(14)->format('Y-m-d')) }}" class="form-control">
                    </div>
                    <div class="mb-0">
                        <label class="form-label">Catatan (opsional)</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $quotation?->notes) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="noc-stat-card bg-white p-4 mb-3">
                    <h6 class="mb-2">Cek Coverage Lokasi</h6>
                    <div class="form-text mb-2">Klik peta di lokasi calon pelanggan untuk mengecek jangkauan dari titik POP/tower terdekat.</div>
                    <div id="coverage-map" style="height: 300px; border-radius: .5rem;"></div>
                    <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $quotation?->latitude) }}">
                    <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $quotation?->longitude) }}">
                    <div id="coverage-result" class="mt-3"></div>
                </div>
            </div>
        </div>

        <div class="noc-stat-card bg-white p-4 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Rincian Item &amp; Harga</h6>
                <button type="button" id="add-item" class="btn btn-sm btn-outline-primary"><i class="bi bi-plus-lg"></i> Tambah Item</button>
            </div>

            <div class="table-responsive">
                <table class="table align-middle" id="items-table">
                    <thead>
                        <tr>
                            <th>Paket (opsional)</th>
                            <th>Nama Item</th>
                            <th style="width: 90px;">Qty</th>
                            <th style="width: 160px;">Harga Satuan</th>
                            <th style="width: 160px;">Subtotal</th>
                            <th style="width: 40px;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body"></tbody>
                </table>
            </div>

            <div class="row justify-content-end">
                <div class="col-md-4">
                    <table class="table table-sm mb-0">
                        <tr>
                            <td>Subtotal (belum PPN)</td>
                            <td class="text-end" id="sum-subtotal">Rp 0</td>
                        </tr>
                        <tr>
                            <td>PPN (<span id="sum-ppn-rate">{{ rtrim(rtrim(number_format($ppnRate, 2, ',', '.'), '0'), ',') }}</span>%)</td>
                            <td class="text-end" id="sum-ppn">Rp 0</td>
                        </tr>
                        <tr class="fw-bold">
                            <td>Total (sudah PPN)</td>
                            <td class="text-end" id="sum-total">Rp 0</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Simpan Surat Penawaran</button>
        <a href="{{ route('quotations.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            (function () {
                var packages = @json($packagesJson);
                var coveragePoints = @json($coveragePointsJson);
                var ppnRate = {{ $ppnRate }};
                var existingItems = @json($existingItemsJson);

                var rupiah = function (n) {
                    return 'Rp ' + (Math.round(n) || 0).toLocaleString('id-ID');
                };

                // ---- Coverage map ----
                var startLat = parseFloat(document.getElementById('latitude').value) || -6.9808;
                var startLng = parseFloat(document.getElementById('longitude').value) || 108.4834;

                var map = L.map('coverage-map').setView([startLat, startLng], 11);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(map);

                coveragePoints.forEach(function (p) {
                    L.marker([p.lat, p.lng]).addTo(map).bindPopup(p.name);
                    L.circle([p.lat, p.lng], { radius: p.radius_km * 1000, color: '#2f6fed', fillOpacity: 0.08 }).addTo(map);
                });

                var prospectMarker = null;

                function haversineKm(lat1, lng1, lat2, lng2) {
                    var R = 6371;
                    var dLat = (lat2 - lat1) * Math.PI / 180;
                    var dLng = (lng2 - lng1) * Math.PI / 180;
                    var a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) ** 2;
                    return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
                }

                function checkCoverage(lat, lng) {
                    document.getElementById('latitude').value = lat.toFixed(7);
                    document.getElementById('longitude').value = lng.toFixed(7);

                    if (prospectMarker) map.removeLayer(prospectMarker);
                    prospectMarker = L.marker([lat, lng], { title: 'Lokasi Prospek' }).addTo(map);

                    var nearest = null, nearestDist = null;
                    coveragePoints.forEach(function (p) {
                        var d = haversineKm(lat, lng, p.lat, p.lng);
                        if (nearestDist === null || d < nearestDist) { nearestDist = d; nearest = p; }
                    });

                    var el = document.getElementById('coverage-result');
                    if (!nearest) {
                        el.innerHTML = '<div class="alert alert-warning mb-0">Belum ada titik coverage terdaftar.</div>';
                        return;
                    }

                    var covered = nearestDist <= nearest.radius_km;
                    if (covered) {
                        el.innerHTML = '<div class="alert alert-success mb-0"><i class="bi bi-check-circle"></i> Tercover — jarak ' + nearestDist.toFixed(2) + ' km dari ' + nearest.name + ' (radius ' + nearest.radius_km + ' km).</div>';
                    } else {
                        el.innerHTML = '<div class="alert alert-danger mb-0"><i class="bi bi-x-circle"></i> Belum tercover — titik terdekat ' + nearest.name + ' berjarak ' + nearestDist.toFixed(2) + ' km (radius ' + nearest.radius_km + ' km).</div>';
                    }
                }

                map.on('click', function (e) { checkCoverage(e.latlng.lat, e.latlng.lng); });

                if (parseFloat(document.getElementById('latitude').value)) {
                    checkCoverage(startLat, startLng);
                }

                // ---- Items ----
                var itemIndex = 0;
                var tbody = document.getElementById('items-body');

                function packageOptions(selectedId) {
                    var html = '<option value="">- Custom -</option>';
                    packages.forEach(function (p) {
                        html += '<option value="' + p.id + '" data-price="' + p.price + '" data-name="' + p.name.replace(/"/g, '&quot;') + (p.speed_label ? ' (' + p.speed_label + ')' : '') + '"' + (String(selectedId) === String(p.id) ? ' selected' : '') + '>' + p.name + (p.speed_label ? ' (' + p.speed_label + ')' : '') + '</option>';
                    });
                    return html;
                }

                function addRow(data) {
                    data = data || {};
                    var idx = itemIndex++;
                    var tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td><select class="form-select form-select-sm pkg-select">' + packageOptions(data.service_package_id) + '</select></td>' +
                        '<td><input type="text" class="form-control form-control-sm item-name" name="items[' + idx + '][item_name]" value="' + (data.item_name ? data.item_name.replace(/"/g, '&quot;') : '') + '" required></td>' +
                        '<td><input type="number" min="1" class="form-control form-control-sm item-qty" name="items[' + idx + '][qty]" value="' + (data.qty || 1) + '" required></td>' +
                        '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm item-price" name="items[' + idx + '][unit_price]" value="' + (data.unit_price || 0) + '" required></td>' +
                        '<td class="row-subtotal text-end">Rp 0</td>' +
                        '<td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>';

                    var pkgSelect = tr.querySelector('.pkg-select');
                    var nameInput = tr.querySelector('.item-name');
                    var priceInput = tr.querySelector('.item-price');
                    var hiddenPkgId = document.createElement('input');
                    hiddenPkgId.type = 'hidden';
                    hiddenPkgId.name = 'items[' + idx + '][service_package_id]';
                    hiddenPkgId.value = data.service_package_id || '';
                    tr.appendChild(hiddenPkgId);

                    pkgSelect.addEventListener('change', function () {
                        var opt = pkgSelect.options[pkgSelect.selectedIndex];
                        hiddenPkgId.value = pkgSelect.value;
                        if (pkgSelect.value) {
                            nameInput.value = opt.getAttribute('data-name');
                            priceInput.value = opt.getAttribute('data-price');
                            recalc();
                        }
                    });

                    tr.querySelector('.remove-row').addEventListener('click', function () {
                        tr.remove();
                        recalc();
                    });

                    tr.querySelector('.item-qty').addEventListener('input', recalc);
                    priceInput.addEventListener('input', recalc);

                    tbody.appendChild(tr);
                    recalc();
                }

                function recalc() {
                    var subtotal = 0;
                    tbody.querySelectorAll('tr').forEach(function (tr) {
                        var qty = parseFloat(tr.querySelector('.item-qty').value) || 0;
                        var price = parseFloat(tr.querySelector('.item-price').value) || 0;
                        var lineSubtotal = qty * price;
                        tr.querySelector('.row-subtotal').textContent = rupiah(lineSubtotal);
                        subtotal += lineSubtotal;
                    });

                    var ppnAmount = subtotal * (ppnRate / 100);
                    document.getElementById('sum-subtotal').textContent = rupiah(subtotal);
                    document.getElementById('sum-ppn').textContent = rupiah(ppnAmount);
                    document.getElementById('sum-total').textContent = rupiah(subtotal + ppnAmount);
                }

                document.getElementById('add-item').addEventListener('click', function () { addRow(); });

                if (existingItems.length) {
                    existingItems.forEach(function (i) { addRow(i); });
                } else {
                    addRow();
                }
            })();
        </script>
    @endpush
@endsection
