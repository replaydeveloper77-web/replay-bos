@extends('layouts.app')

@section('title', 'Pelanggan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Data Pelanggan</h5>
        @can('create', \App\Models\Customer::class)
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#importSpreadsheetModal">
                    <i class="bi bi-file-earmark-spreadsheet me-1"></i> Import Spreadsheet
                </button>
                <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Tambah Pelanggan
                </a>
            </div>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('import_errors'))
        <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
            <strong><i class="bi bi-exclamation-circle me-1"></i> Perhatian saat Import Data:</strong>
            <ul class="mb-0 mt-1 small">
                @foreach (session('import_errors') as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('import_result'))
        @php $res = session('import_result'); @endphp
        <div class="card border-success mb-3 shadow-sm">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center py-2">
                <span><i class="bi bi-check2-circle me-1"></i> <strong>Ringkasan Hasil Import Spreadsheet</strong></span>
                <span class="badge bg-light text-success fw-bold">{{ $res['success_count'] }} Data Berhasil Ditambahkan</span>
            </div>
            <div class="card-body p-3">
                <p class="mb-2 small text-muted">Pelanggan berikut telah berhasil disimpan dan diberikan nomor layanan otomatis:</p>
                <div class="table-responsive" style="max-height: 240px; overflow-y: auto;">
                    <table class="table table-sm table-striped mb-0 small align-middle">
                        <thead>
                            <tr>
                                <th>No. Layanan</th>
                                <th>Nama Pelanggan</th>
                                <th>Kategori</th>
                                <th>Perusahaan</th>
                                <th>Layanan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($res['customers'] as $c)
                                <tr>
                                    <td><span class="badge bg-primary font-monospace">{{ $c['service_number'] }}</span></td>
                                    <td><strong>{{ $c['name'] }}</strong></td>
                                    <td><span class="badge bg-secondary">{{ ucfirst(strtolower($c['category'])) }}</span></td>
                                    <td>{{ $c['company'] ?? '-' }}</td>
                                    <td>{{ $c['service'] ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                @if (!empty($res['errors']))
                    <div class="mt-3 alert alert-warning mb-0 py-2 small">
                        <strong>Catatan/Peringatan Baris ({{ count($res['errors']) }}):</strong>
                        <ul class="mb-0 mt-1 ps-3">
                            @foreach ($res['errors'] as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <ul class="nav nav-pills mb-2">
        <li class="nav-item"><a class="nav-link {{ ! request('category') ? 'active' : '' }}" href="{{ route('customers.index', request()->except(['category', 'page'])) }}">Semua</a></li>
        @foreach (\App\Models\Customer::CATEGORIES as $cat)
            <li class="nav-item"><a class="nav-link {{ request('category') === $cat ? 'active' : '' }}" href="{{ route('customers.index', array_merge(request()->except('page'), ['category' => $cat])) }}">{{ ucfirst(strtolower($cat)) }}</a></li>
        @endforeach
    </ul>

    <form method="GET" class="mb-3">
        @if (request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" style="max-width: 300px;" placeholder="Cari nama/perusahaan/no. layanan/IP...">
    </form>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Perusahaan</th>
                        <th>PIC</th>
                        <th>WhatsApp</th>
                        <th>Layanan</th>
                        <th>No. Layanan</th>
                        <th>IP</th>
                        <th class="text-center">Ticket</th>
                        <th>Status</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td><a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a></td>
                            <td><span class="badge bg-secondary">{{ ucfirst(strtolower($customer->category)) }}</span></td>
                            <td>{{ $customer->company ?? '-' }}</td>
                            <td>{{ $customer->pic_name ?? '-' }}</td>
                            <td>{{ $customer->whatsapp ?? '-' }}</td>
                            <td>{{ $customer->service ?? '-' }}</td>
                            <td>{{ $customer->service_number ?? '-' }}</td>
                            <td class="small">
                                @forelse ($customer->ipAllocations as $ip)
                                    <div><code>{{ $ip->ip_address }}</code></div>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td class="text-center">{{ $customer->tickets_count }}</td>
                            <td>
                                @if ($customer->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                                @if ($customer->isPendingActivation())
                                    <span class="badge bg-warning text-dark">Menunggu Aktivasi</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-grid gap-1 flex-grow-1">
                                        @can('activate', \App\Models\Customer::class)
                                            @if ($customer->isPendingActivation())
                                                <form method="POST" action="{{ route('customers.activate', $customer) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-warning w-100" onclick="return confirm('Tandai pelanggan ini sudah selesai diaktivasi?')">Tandai Aktivasi Selesai</button>
                                                </form>
                                            @endif
                                        @endcan
                                        @can('update', \App\Models\Customer::class)
                                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @if ($customer->is_active)
                                                <form method="POST" action="{{ route('customers.disable', $customer) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Nonaktifkan pelanggan ini?')">Nonaktifkan</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('customers.enable', $customer) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success w-100">Aktifkan</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                    @can('delete', \App\Models\Customer::class)
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan {{ $customer->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0" title="Hapus Pelanggan" style="line-height: 1;">
                                                <i class="bi bi-trash fs-5 text-danger"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-muted text-center py-3">Belum ada data pelanggan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $customers->links() }}
    </div>

    @can('create', \App\Models\Customer::class)
        <!-- Modal Import Spreadsheet -->
        <div class="modal fade" id="importSpreadsheetModal" tabindex="-1" aria-labelledby="importSpreadsheetModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('customers.import') }}" enctype="multipart/form-data" id="importSpreadsheetForm">
                        @csrf
                        <div class="modal-header">
                            <div>
                                <h5 class="modal-title d-flex align-items-center gap-2" id="importSpreadsheetModalLabel">
                                    <i class="bi bi-file-earmark-spreadsheet-fill text-success fs-4"></i>
                                    <span>Import Data Pelanggan via Spreadsheet</span>
                                </h5>
                                <p class="text-muted small mb-0 mt-1">Input banyak data pelanggan sekaligus via Excel/CSV. Nomor layanan akan di-generate otomatis.</p>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body p-4">
                            <!-- Download Template Section -->
                            <div class="p-3 bg-light rounded border mb-4">
                                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
                                    <div>
                                        <div class="fw-semibold text-dark"><i class="bi bi-download me-1 text-primary"></i> Unduh Format Template</div>
                                        <div class="small text-muted">Gunakan template resmi untuk format kolom yang rapi &amp; sesuai standar.</div>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('customers.import.template', ['format' => 'xlsx']) }}" class="btn btn-sm btn-outline-success">
                                            <i class="bi bi-file-earmark-excel me-1"></i> Template Excel (.xlsx)
                                        </a>
                                        <a href="{{ route('customers.import.template', ['format' => 'csv']) }}" class="btn btn-sm btn-outline-secondary">
                                            <i class="bi bi-filetype-csv me-1"></i> Template CSV (.csv)
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Upload Inputs -->
                            <div class="mb-3">
                                <label for="spreadsheetFile" class="form-label fw-semibold">Pilih File Spreadsheet <span class="text-danger">*</span></label>
                                <input type="file" name="file" id="spreadsheetFile" class="form-control" accept=".xlsx,.xls,.csv" required>
                                <div class="form-text small">Mendukung format file <code>.xlsx</code>, <code>.xls</code>, atau <code>.csv</code> (Maks. 10 MB).</div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Kategori Default</label>
                                    <select name="default_category" class="form-select form-select-sm">
                                        <option value="HOME">Home (Nomor layanan: 9202300001xx)</option>
                                        <option value="CORPORATE">Corporate (Nomor layanan: 9202200001xx)</option>
                                        <option value="RESELLER">Reseller (Nomor layanan: 9202100001xx)</option>
                                    </select>
                                    <div class="form-text small">Kategori yang dipakai jika kolom 'Kategori' di spreadsheet tidak diisi.</div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label fw-semibold">Penomoran Layanan</label>
                                    <div class="form-control form-control-sm bg-light text-muted">
                                        <i class="bi bi-magic text-warning me-1"></i> Otomatis diisi sistem per baris
                                    </div>
                                    <div class="form-text small">Nomor layanan unik (920210000[Urut] Reseller / 920220000[Urut] Corporate / 920230000[Urut] Home) dibuat otomatis.</div>
                                </div>
                            </div>

                            <!-- Guide Table of Columns -->
                            <div class="card bg-white border">
                                <div class="card-header bg-light py-2 px-3 fw-semibold small text-secondary">
                                    <i class="bi bi-info-circle me-1"></i> Panduan Kolom Spreadsheet
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0 small" style="font-size: 0.82rem;">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Nama Kolom</th>
                                                    <th>Sifat</th>
                                                    <th>Keterangan / Contoh</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="fw-semibold">Nama Pelanggan</td>
                                                    <td><span class="badge bg-danger">Wajib</span></td>
                                                    <td>Nama pelanggan/client (misal: <em>Budi Santoso</em> / <em>PT Digital Sejahtera</em>)</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Nama Perusahaan</td>
                                                    <td><span class="badge bg-secondary">Opsional</span></td>
                                                    <td>Nama PT/CV/Usaha (kosongkan jika perorangan)</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Nama PIC</td>
                                                    <td><span class="badge bg-secondary">Opsional</span></td>
                                                    <td>Nama contact person / PIC pelanggan</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">No WhatsApp</td>
                                                    <td><span class="badge bg-secondary">Opsional</span></td>
                                                    <td>Nomor telepon/WA aktif (misal: <code>081234567890</code>)</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Email</td>
                                                    <td><span class="badge bg-secondary">Opsional</span></td>
                                                    <td>Email aktif pelanggan (misal: <code>budi@example.com</code>)</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Lokasi / Alamat</td>
                                                    <td><span class="badge bg-secondary">Opsional</span></td>
                                                    <td>Alamat lengkap instalasi/pemasangan</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Layanan</td>
                                                    <td><span class="badge bg-secondary">Opsional</span></td>
                                                    <td>Jenis/paket layanan (misal: <em>Internet Dedicated 100 Mbps</em>)</td>
                                                </tr>
                                                <tr>
                                                    <td class="fw-semibold">Kategori</td>
                                                    <td><span class="badge bg-secondary">Opsional</span></td>
                                                    <td><code>HOME</code>, <code>CORPORATE</code>, atau <code>RESELLER</code></td>
                                                </tr>
                                                <tr class="table-light">
                                                    <td class="fw-semibold text-primary">Nomor Layanan</td>
                                                    <td><span class="badge bg-info text-dark">Otomatis</span></td>
                                                    <td><strong>Kosongkan kolom ini</strong> agar sistem mengisi otomatis secara urut (contoh: <code>9202100001</code>, <code>9202200001</code>, <code>9202300001</code>)!</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer bg-light py-2 px-4">
                            <button type="button" class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-sm btn-success" id="btnSubmitImport">
                                <i class="bi bi-upload me-1"></i> Mulai Import Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @push('scripts')
        <script>
            document.getElementById('importSpreadsheetForm')?.addEventListener('submit', function () {
                var btn = document.getElementById('btnSubmitImport');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Mengimpor data...';
                }
            });
        </script>
    @endpush
@endsection
