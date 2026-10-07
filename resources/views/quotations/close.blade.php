@extends('layouts.app')

@section('title', 'Proses Closing')

@section('content')
    <h5 class="mb-1">Proses Closing — {{ $quotation->quotation_number }}</h5>
    <p class="text-muted small mb-3">{{ $quotation->prospect_name }} @if($quotation->prospect_company) ({{ $quotation->prospect_company }}) @endif</p>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-warning">
        <i class="bi bi-info-circle"></i> Sesuaikan harga di bawah kalau ada kesepakatan akhir yang beda dari Surat Penawaran. Setelah disimpan, sistem akan buat <strong>Invoice</strong> dan pelanggan ini otomatis masuk ke daftar Pelanggan (menunggu aktivasi NOC).
    </div>

    <form method="POST" action="{{ route('quotations.close', $quotation) }}">
        @csrf

        <div class="noc-stat-card bg-white p-4 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Rincian Harga Sesuai Kesepakatan</h6>
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
                            <td>PPN ({{ rtrim(rtrim(number_format($quotation->ppn_rate, 2, ',', '.'), '0'), ',') }}%)</td>
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

        <button type="submit" class="btn btn-warning">
            <i class="bi bi-check2-circle"></i> Proses Closing &amp; Buat Invoice
        </button>
        <a href="{{ route('quotations.show', $quotation) }}" class="btn btn-outline-secondary">Batal</a>
    </form>

    @push('scripts')
        <script>
            (function () {
                var packages = @json($packagesJson);
                var ppnRate = {{ (float) $quotation->ppn_rate }};
                var existingItems = @json($itemsJson);

                var rupiah = function (n) {
                    return 'Rp ' + (Math.round(n) || 0).toLocaleString('id-ID');
                };

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
