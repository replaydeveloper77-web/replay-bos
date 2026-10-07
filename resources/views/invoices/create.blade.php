@extends('layouts.app')

@section('title', 'Buat Invoice Manual')

@section('content')
    <h5 class="mb-3">Buat Invoice Manual (Upgrade Paket / Lainnya)</h5>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('invoices.store') }}">
        @csrf

        <div class="noc-stat-card bg-white p-4 mb-3" style="max-width: 720px;">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Pelanggan</label>
                    <select name="customer_id" class="form-select" required>
                        <option value="">- Pilih Pelanggan -</option>
                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}" @selected(old('customer_id') == $c->id)>{{ $c->name }} @if($c->company) ({{ $c->company }}) @endif</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Jenis Invoice</label>
                    <select name="type" class="form-select" required>
                        <option value="UPGRADE" @selected(old('type') === 'UPGRADE')>Upgrade Paket</option>
                        <option value="MANUAL" @selected(old('type') === 'MANUAL')>Lainnya (Manual)</option>
                    </select>
                </div>
                <div class="col-md-3 mb-3">
                    <label class="form-label">Jatuh Tempo</label>
                    <input type="date" name="due_date" value="{{ old('due_date', now()->addDays(7)->format('Y-m-d')) }}" class="form-control" required>
                </div>
            </div>
        </div>

        <div class="noc-stat-card bg-white p-4 mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Rincian Harga</h6>
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
                            <td>PPN</td>
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

        <button type="submit" class="btn btn-primary">Simpan Invoice</button>
        <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">Batal</a>
    </form>

    @push('scripts')
        <script>
            (function () {
                var packages = @json($packagesJson);
                var ppnRate = {{ $ppnRate }};

                var rupiah = function (n) {
                    return 'Rp ' + (Math.round(n) || 0).toLocaleString('id-ID');
                };

                var itemIndex = 0;
                var tbody = document.getElementById('items-body');

                function packageOptions() {
                    var html = '<option value="">- Custom -</option>';
                    packages.forEach(function (p) {
                        html += '<option value="' + p.id + '" data-price="' + p.price + '" data-name="' + p.name.replace(/"/g, '&quot;') + (p.speed_label ? ' (' + p.speed_label + ')' : '') + '">' + p.name + (p.speed_label ? ' (' + p.speed_label + ')' : '') + '</option>';
                    });
                    return html;
                }

                function addRow() {
                    var idx = itemIndex++;
                    var tr = document.createElement('tr');
                    tr.innerHTML =
                        '<td><select class="form-select form-select-sm pkg-select">' + packageOptions() + '</select></td>' +
                        '<td><input type="text" class="form-control form-control-sm item-name" name="items[' + idx + '][item_name]" required></td>' +
                        '<td><input type="number" min="1" class="form-control form-control-sm item-qty" name="items[' + idx + '][qty]" value="1" required></td>' +
                        '<td><input type="number" step="0.01" min="0" class="form-control form-control-sm item-price" name="items[' + idx + '][unit_price]" value="0" required></td>' +
                        '<td class="row-subtotal text-end">Rp 0</td>' +
                        '<td><button type="button" class="btn btn-sm btn-outline-danger remove-row"><i class="bi bi-trash"></i></button></td>';

                    var pkgSelect = tr.querySelector('.pkg-select');
                    var nameInput = tr.querySelector('.item-name');
                    var priceInput = tr.querySelector('.item-price');
                    var hiddenPkgId = document.createElement('input');
                    hiddenPkgId.type = 'hidden';
                    hiddenPkgId.name = 'items[' + idx + '][service_package_id]';
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

                document.getElementById('add-item').addEventListener('click', addRow);
                addRow();
            })();
        </script>
    @endpush
@endsection
