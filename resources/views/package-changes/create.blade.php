@extends('layouts.app')

@section('title', 'Ajukan Upgrade / Downgrade Paket')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-arrow-down-up text-primary me-2"></i>Form Pengajuan Upgrade / Downgrade Paket
            </h5>
            <small class="text-muted">Ajukan permohonan perubahan paket layanan untuk diverifikasi dan disetujui oleh Finance.</small>
        </div>
        <a href="{{ route('package-changes.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Kembali
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle me-1"></i> Periksa kembali data formulir:</div>
            <ul class="mb-0 ps-3">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @php
        $countReseller = $customers->where('category', 'RESELLER')->count();
        $countCorporate = $customers->where('category', 'CORPORATE')->count();
        $countHome = $customers->where('category', 'HOME')->count();
        $countTotal = $customers->count();

        $customersJson = $customers->map(function ($c) {
            return [
                'id' => $c->id,
                'name' => $c->name,
                'category' => $c->category ?? 'HOME',
                'category_label' => match($c->category) {
                    'RESELLER' => 'Reseller',
                    'CORPORATE' => 'Corporate',
                    'HOME' => 'Home',
                    default => 'Lainnya',
                },
                'package_name' => $c->servicePackage?->name ?: ($c->service ?: 'Belum ada paket'),
                'price' => (float) ($c->subscription_price ?? ($c->servicePackage?->price ?? 0)),
                'service_number' => $c->service_number ?? '-',
            ];
        })->values();
    @endphp

    <div class="row">
        <div class="col-lg-8">
            <div class="noc-stat-card bg-white p-4">
                <form action="{{ route('package-changes.store') }}" method="POST">
                    @csrf

                    {{-- 1. Filter Kategori Pelanggan & Dropdown Pelanggan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Pilih Kategori Client / Pelanggan <span class="text-danger">*</span>
                        </label>
                        <div class="btn-group w-100 mb-2" role="group" id="clientCategoryFilterGroup">
                            <input type="radio" class="btn-check" name="client_category_filter" id="filter_all" value="ALL" autocomplete="off" checked>
                            <label class="btn btn-outline-primary btn-sm py-2" for="filter_all">
                                <i class="bi bi-grid-fill me-1"></i> Semua Client <span class="badge bg-secondary ms-1">{{ $countTotal }}</span>
                            </label>

                            <input type="radio" class="btn-check" name="client_category_filter" id="filter_reseller" value="RESELLER" autocomplete="off">
                            <label class="btn btn-outline-primary btn-sm py-2" for="filter_reseller">
                                <i class="bi bi-people-fill me-1"></i> Reseller <span class="badge bg-secondary ms-1">{{ $countReseller }}</span>
                            </label>

                            <input type="radio" class="btn-check" name="client_category_filter" id="filter_corporate" value="CORPORATE" autocomplete="off">
                            <label class="btn btn-outline-primary btn-sm py-2" for="filter_corporate">
                                <i class="bi bi-building me-1"></i> Corporate <span class="badge bg-secondary ms-1">{{ $countCorporate }}</span>
                            </label>

                            <input type="radio" class="btn-check" name="client_category_filter" id="filter_home" value="HOME" autocomplete="off">
                            <label class="btn btn-outline-primary btn-sm py-2" for="filter_home">
                                <i class="bi bi-house-door-fill me-1"></i> Home <span class="badge bg-secondary ms-1">{{ $countHome }}</span>
                            </label>
                        </div>
                        <div class="form-text mb-2">
                            Pilih salah satu kategori di atas untuk memfilter daftar pelanggan (hanya client kategori tersebut yang akan muncul).
                        </div>

                        <label for="customer_id" class="form-label fw-semibold">
                            Pilih Pelanggan <span class="text-danger">*</span>
                        </label>
                        <select name="customer_id" id="customer_id" class="form-select @error('customer_id') is-invalid @enderror" required>
                            <option value="">-- Pilih Pelanggan --</option>
                        </select>
                        @error('customer_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- Current Package Info Card --}}
                    <div id="current_package_box" class="alert alert-light border d-none mb-3 py-2 px-3">
                        <div class="row align-items-center">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Kategori &amp; No. ID Layanan:</small>
                                <span id="current_category_badge" class="badge bg-secondary me-1">-</span>
                                <code id="current_service_number" class="small">-</code>
                            </div>
                            <div class="col-md-5 mt-2 mt-md-0">
                                <small class="text-muted d-block">Paket Saat Ini:</small>
                                <strong id="current_package_name" class="text-dark fs-6">-</strong>
                            </div>
                            <div class="col-md-3 text-md-end mt-2 mt-md-0">
                                <small class="text-muted d-block">Tarif Langganan:</small>
                                <strong id="current_package_price" class="text-primary fs-6">Rp 0</strong>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Jenis Perubahan --}}
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Jenis Perubahan <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_upgrade" value="UPGRADE"
                                    @checked(old('type', 'UPGRADE') === 'UPGRADE')>
                                <label class="form-check-label fw-semibold text-success" for="type_upgrade">
                                    <i class="bi bi-arrow-up-circle me-1"></i> Upgrade Paket (Naik Paket/Kecepatan)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="type" id="type_downgrade" value="DOWNGRADE"
                                    @checked(old('type') === 'DOWNGRADE')>
                                <label class="form-check-label fw-semibold text-warning" for="type_downgrade">
                                    <i class="bi bi-arrow-down-circle me-1"></i> Downgrade Paket (Turun Paket/Kecepatan)
                                </label>
                            </div>
                        </div>
                        @error('type')
                            <div class="text-danger small mt-1">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 3. Paket Baru Tujuan (Ketik Saja) --}}
                    <div class="mb-3">
                        <label for="target_package" class="form-label fw-semibold">
                            Paket Baru Tujuan <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-primary"><i class="bi bi-hdd-network"></i></span>
                            <input type="text"
                                name="target_package"
                                id="target_package"
                                list="packages_datalist"
                                class="form-control @error('target_package') is-invalid @enderror"
                                value="{{ old('target_package') }}"
                                placeholder="Ketik nama paket baru tujuan (contoh: Dedicated 50 Mbps, Home 30 Mbps, IP Transit, dll)..."
                                required
                                autocomplete="off">
                        </div>
                        <div class="form-text">
                            Ketik bebas nama paket yang diajukan. Sistem juga menampilkan saran dari daftar katalog paket yang tersedia saat Anda mengetik.
                        </div>
                        <datalist id="packages_datalist">
                            @foreach ($servicePackages as $pkg)
                                <option value="{{ $pkg->name }}">{{ $pkg->name }} @if($pkg->speed_label) ({{ $pkg->speed_label }}) @endif</option>
                            @endforeach
                        </datalist>
                        @error('target_package')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- 4. Alasan / Catatan Pengajuan --}}
                    <div class="mb-4">
                        <label for="notes" class="form-label fw-semibold">Alasan &amp; Catatan Pengajuan</label>
                        <textarea name="notes" id="notes" rows="3" class="form-control @error('notes') is-invalid @enderror"
                            placeholder="Contoh: Permintaan pelanggan untuk upgrade bandwidth kantor, sudah konfirmasi via tiket / WhatsApp.">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="{{ route('package-changes.index') }}" class="btn btn-outline-secondary">Batal</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-send me-1"></i> Kirim Pengajuan ke Finance
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="col-lg-4 mt-3 mt-lg-0">
            <div class="noc-stat-card bg-white p-3 mb-3 border-start border-4 border-info">
                <h6 class="fw-bold mb-2"><i class="bi bi-info-circle text-info me-1"></i> Alur Kerja Otomatis:</h6>
                <ol class="small text-muted ps-3 mb-0" style="line-height: 1.6;">
                    <li><strong>Ajukan Permohonan:</strong> Pilih client berdasarkan kategori (Reseller / Corporate / Home), ketik nama paket baru tujuan, lalu kirim ke Finance.</li>
                    <li><strong>Review Finance:</strong> Tim Finance memverifikasi pengajuan dan menyesuaikan tarif tagihan penagihan.</li>
                    <li><strong>Generate WO Otomatis:</strong> Saat Finance mengklik <em>Setujui</em>, sistem otomatis <strong>men-generate nomor Work Order Upgrade</strong> tanpa perlu input manual.</li>
                    <li><strong>Update Paket:</strong> Master data paket pelanggan langsung terbarui ke paket baru.</li>
                </ol>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const customerSelect = document.getElementById('customer_id');
        const currentPackageBox = document.getElementById('current_package_box');
        const currentPackageName = document.getElementById('current_package_name');
        const currentPackagePrice = document.getElementById('current_package_price');
        const currentCategoryBadge = document.getElementById('current_category_badge');
        const currentServiceNumber = document.getElementById('current_service_number');

        // Customers Data from Backend
        const allCustomers = @json($customersJson);
        const preselectedCustomerId = "{{ old('customer_id', $selectedCustomerId) }}";

        const categoryRadios = document.querySelectorAll('input[name="client_category_filter"]');

        function getCategoryLabel(cat) {
            switch(cat) {
                case 'RESELLER': return 'Reseller';
                case 'CORPORATE': return 'Corporate';
                case 'HOME': return 'Home';
                default: return 'Lainnya';
            }
        }

        function renderCustomers(selectedCategory, preserveId = null) {
            customerSelect.innerHTML = '';

            const filtered = allCustomers.filter(function (c) {
                if (selectedCategory === 'ALL') return true;
                return c.category === selectedCategory;
            });

            if (filtered.length === 0) {
                const emptyOption = document.createElement('option');
                emptyOption.value = '';
                emptyOption.textContent = '-- Tidak ada client ' + getCategoryLabel(selectedCategory) + ' terdaftar --';
                customerSelect.appendChild(emptyOption);
                updateCustomerInfo();
                return;
            }

            const defaultOption = document.createElement('option');
            defaultOption.value = '';
            defaultOption.textContent = selectedCategory === 'ALL'
                ? '-- Pilih Pelanggan --'
                : '-- Pilih Pelanggan ' + getCategoryLabel(selectedCategory) + ' (' + filtered.length + ') --';
            customerSelect.appendChild(defaultOption);

            if (selectedCategory === 'ALL') {
                const groups = {
                    'RESELLER': { label: 'Reseller (Layanan 92021...)', items: [] },
                    'CORPORATE': { label: 'Corporate (Layanan 92022...)', items: [] },
                    'HOME': { label: 'Home (Layanan 92023...)', items: [] },
                    'OTHER': { label: 'Lainnya', items: [] },
                };

                filtered.forEach(function (c) {
                    if (groups[c.category]) {
                        groups[c.category].items.push(c);
                    } else {
                        groups['OTHER'].items.push(c);
                    }
                });

                ['RESELLER', 'CORPORATE', 'HOME', 'OTHER'].forEach(function (catKey) {
                    const grp = groups[catKey];
                    if (grp.items.length > 0) {
                        const optgroup = document.createElement('optgroup');
                        optgroup.label = grp.label;
                        grp.items.forEach(function (c) {
                            const opt = document.createElement('option');
                            opt.value = c.id;
                            opt.textContent = c.name + ' (' + c.service_number + ')';
                            if (preserveId && String(preserveId) === String(c.id)) {
                                opt.selected = true;
                            }
                            optgroup.appendChild(opt);
                        });
                        customerSelect.appendChild(optgroup);
                    }
                });
            } else {
                // HANYA masukkan pelanggan dari kategori yang dipilih ke dalam DOM
                filtered.forEach(function (c) {
                    const opt = document.createElement('option');
                    opt.value = c.id;
                    opt.textContent = c.name + ' (' + c.service_number + ')';
                    if (preserveId && String(preserveId) === String(c.id)) {
                        opt.selected = true;
                    }
                    customerSelect.appendChild(opt);
                });
            }

            updateCustomerInfo();
        }

        categoryRadios.forEach(function (radio) {
            radio.addEventListener('change', function () {
                if (this.checked) {
                    renderCustomers(this.value);
                }
            });
        });

        function updateCustomerInfo() {
            const selectedId = customerSelect.value;
            const c = allCustomers.find(function (item) {
                return String(item.id) === String(selectedId);
            });

            if (c) {
                currentPackageName.textContent = c.package_name;
                currentPackagePrice.textContent = c.price > 0 ? ('Rp ' + c.price.toLocaleString('id-ID') + '/bln') : '-';
                currentCategoryBadge.textContent = c.category_label;
                currentServiceNumber.textContent = c.service_number;
                currentPackageBox.classList.remove('d-none');
            } else {
                currentPackageBox.classList.add('d-none');
            }
        }

        customerSelect.addEventListener('change', updateCustomerInfo);

        // Initial setup on load
        let initialCategory = 'ALL';
        if (preselectedCustomerId) {
            const found = allCustomers.find(function (item) {
                return String(item.id) === String(preselectedCustomerId);
            });
            if (found && found.category) {
                initialCategory = found.category;
                const matchedRadio = document.querySelector('input[name="client_category_filter"][value="' + initialCategory + '"]');
                if (matchedRadio) {
                    matchedRadio.checked = true;
                }
            }
        }

        renderCustomers(initialCategory, preselectedCustomerId);
    });
</script>
@endpush
