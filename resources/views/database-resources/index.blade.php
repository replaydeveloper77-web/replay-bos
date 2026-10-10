@extends('layouts.app')

@section('title', 'Resource Database & Server Monitoring')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-database text-primary me-2"></i>Resource Database &amp; Server Monitoring (cPanel)
            </h5>
            <small class="text-muted">Pantau penggunaan kapasitas database MySQL, rincian tabel, koneksi aktif, serta resource hosting cPanel.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('database-resources.index') }}" class="btn btn-outline-secondary btn-sm" title="Segarkan Data">
                <i class="bi bi-arrow-clockwise me-1"></i> Refresh
            </a>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#optimizeAllModal">
                <i class="bi bi-tools me-1"></i> Optimasi Semua Tabel
            </button>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-1"></i> {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Top Stat Cards Grid --}}
    <div class="row g-3 mb-3">
        {{-- Card 1: Total Ukuran Database --}}
        <div class="col-sm-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3 h-100 border-start border-4 border-primary">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted fw-semibold">Ukuran Total Database</small>
                    <i class="bi bi-database-fill text-primary fs-5"></i>
                </div>
                <h4 class="mb-1 fw-bold text-dark">{{ $overview['database']['total_formatted'] }}</h4>
                <div class="small text-muted mb-2">
                    Data: <span class="fw-semibold text-dark">{{ $overview['database']['data_formatted'] }}</span> | Indeks: <span class="fw-semibold text-dark">{{ $overview['database']['index_formatted'] }}</span>
                </div>
                @if ($overview['database']['overhead_bytes'] > 0)
                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle small">
                        Overhead: {{ $overview['database']['overhead_formatted'] }}
                    </span>
                @else
                    <span class="badge bg-success-subtle text-success border border-success-subtle small">
                        Penyimpanan Optimal
                    </span>
                @endif
            </div>
        </div>

        {{-- Card 2: Jumlah Tabel & Records --}}
        <div class="col-sm-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3 h-100 border-start border-4 border-info">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted fw-semibold">Tabel &amp; Baris Data</small>
                    <i class="bi bi-table text-info fs-5"></i>
                </div>
                <h4 class="mb-1 fw-bold text-dark">{{ number_format($overview['database']['total_tables']) }} <span class="fs-6 fw-normal text-muted">Tabel</span></h4>
                <div class="small text-muted mb-2">
                    Total Records: <strong class="text-dark">{{ number_format($overview['database']['total_rows']) }}</strong> baris
                </div>
                <span class="badge bg-info-subtle text-info border border-info-subtle small">
                    Engine: InnoDB
                </span>
            </div>
        </div>

        {{-- Card 3: Koneksi Database cPanel MySQL --}}
        <div class="col-sm-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3 h-100 border-start border-4 border-success">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted fw-semibold">Koneksi Database Aktif</small>
                    <i class="bi bi-hdd-network text-success fs-5"></i>
                </div>
                <h4 class="mb-1 fw-bold text-dark">
                    {{ $overview['database']['threads_connected'] }}
                    <span class="fs-6 fw-normal text-muted">/ {{ $overview['database']['max_connections'] }} Max</span>
                </h4>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ max($overview['database']['connection_percent'], 2) }}%"></div>
                </div>
                <div class="small text-muted d-flex justify-content-between">
                    <span>{{ $overview['database']['connection_percent'] }}% Digunakan</span>
                    <span class="text-success fw-semibold">Normal</span>
                </div>
            </div>
        </div>

        {{-- Card 4: Disk Hosting cPanel --}}
        <div class="col-sm-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3 h-100 border-start border-4 border-secondary">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small class="text-muted fw-semibold">Disk Storage Hosting</small>
                    <i class="bi bi-device-hdd text-secondary fs-5"></i>
                </div>
                <h4 class="mb-1 fw-bold text-dark">{{ $overview['disk']['used_formatted'] }}</h4>
                <div class="progress mb-1" style="height: 6px;">
                    <div class="progress-bar bg-secondary" role="progressbar" style="width: {{ $overview['disk']['used_percent'] }}%"></div>
                </div>
                <div class="small text-muted d-flex justify-content-between">
                    <span>{{ $overview['disk']['used_percent'] }}% Terpakai</span>
                    <span>Sisa: {{ $overview['disk']['free_formatted'] }}</span>
                </div>
            </div>
        </div>
    </div>

    {{-- System & Environment Details Card --}}
    <div class="row g-3 mb-4">
        {{-- Left: MySQL / MariaDB Server Info --}}
        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-3 h-100">
                <h6 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-server text-primary me-2"></i>Spesifikasi Database Server (cPanel MySQL)
                </h6>
                <div class="row g-2 small">
                    <div class="col-6 col-sm-4 text-muted">Database Aktif:</div>
                    <div class="col-6 col-sm-8"><code class="text-primary fw-bold">{{ $overview['database']['name'] }}</code></div>

                    <div class="col-6 col-sm-4 text-muted">Versi Engine:</div>
                    <div class="col-6 col-sm-8 fw-semibold text-dark">{{ $overview['database']['version'] }}</div>

                    <div class="col-6 col-sm-4 text-muted">Host &amp; Port:</div>
                    <div class="col-6 col-sm-8">{{ $overview['database']['host'] }}:{{ $overview['database']['port'] }}</div>

                    <div class="col-6 col-sm-4 text-muted">Charset / Collation:</div>
                    <div class="col-6 col-sm-8">{{ $overview['database']['charset'] }} / {{ $overview['database']['collation'] }}</div>

                    <div class="col-6 col-sm-4 text-muted">Waktu Aktif (Uptime):</div>
                    <div class="col-6 col-sm-8 text-success fw-semibold">{{ $overview['database']['uptime_formatted'] }}</div>

                    <div class="col-6 col-sm-4 text-muted">Total Query Dieksekusi:</div>
                    <div class="col-6 col-sm-8">{{ number_format($overview['database']['total_queries']) }} queries</div>

                    <div class="col-6 col-sm-4 text-muted">Slow Queries:</div>
                    <div class="col-6 col-sm-8">
                        @if ($overview['database']['slow_queries'] > 0)
                            <span class="badge bg-warning text-dark">{{ $overview['database']['slow_queries'] }}</span>
                        @else
                            <span class="badge bg-success-subtle text-success">0 (Tidak ada query lambat)</span>
                        @endif
                    </div>

                    <div class="col-6 col-sm-4 text-muted">Buffer Pool (InnoDB):</div>
                    <div class="col-6 col-sm-8 fw-semibold">{{ $overview['database']['buffer_pool_formatted'] }}</div>

                    <div class="col-6 col-sm-4 text-muted">Max Allowed Packet:</div>
                    <div class="col-6 col-sm-8">{{ $overview['database']['max_packet_formatted'] }}</div>
                </div>
            </div>
        </div>

        {{-- Right: PHP Environment & cPanel Hosting Info --}}
        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-3 h-100">
                <h6 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-cpu text-info me-2"></i>Resource Runtime PHP &amp; Hosting Server
                </h6>
                <div class="row g-2 small">
                    <div class="col-6 col-sm-4 text-muted">Versi PHP:</div>
                    <div class="col-6 col-sm-8 fw-semibold text-dark">PHP {{ $overview['php']['version'] }}</div>

                    <div class="col-6 col-sm-4 text-muted">Memory Limit PHP:</div>
                    <div class="col-6 col-sm-8 fw-semibold text-primary">{{ $overview['php']['memory_limit'] }}</div>

                    <div class="col-6 col-sm-4 text-muted">Memory Saat Ini:</div>
                    <div class="col-6 col-sm-8">{{ $overview['php']['memory_usage_formatted'] }} (Puncak: {{ $overview['php']['memory_peak_formatted'] }})</div>

                    <div class="col-6 col-sm-4 text-muted">Max Execution Time:</div>
                    <div class="col-6 col-sm-8">{{ $overview['php']['max_execution_time'] }}</div>

                    <div class="col-6 col-sm-4 text-muted">Upload Max Filesize:</div>
                    <div class="col-6 col-sm-8">{{ $overview['php']['upload_max_filesize'] }} (Post: {{ $overview['php']['post_max_size'] }})</div>

                    <div class="col-6 col-sm-4 text-muted">Total Disk Server:</div>
                    <div class="col-6 col-sm-8">{{ $overview['disk']['total_formatted'] }}</div>

                    <div class="col-6 col-sm-4 text-muted">Sisa Ruang Disk:</div>
                    <div class="col-6 col-sm-8 text-success fw-semibold">{{ $overview['disk']['free_formatted'] }}</div>

                    <div class="col-12 mt-2 pt-2 border-top">
                        <div class="alert alert-light border py-2 px-3 mb-0 small text-muted">
                            <i class="bi bi-info-circle text-info me-1"></i>
                            <strong>Tips cPanel:</strong> Untuk memelihara performa database di hosting cPanel, gunakan tombol <em>Optimasi</em> pada tabel yang memiliki overhead guna membersihkan ruang kosong (fragmentasi data).
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Table Breakdown Section --}}
    <div class="noc-stat-card bg-white p-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h6 class="fw-bold mb-0">
                    <i class="bi bi-list-columns text-primary me-2"></i>Rincian Kapasitas Seluruh Tabel Database
                </h6>
                <small class="text-muted">Total {{ count($tables) }} tabel terdeteksi di dalam database <code>{{ $overview['database']['name'] }}</code></small>
            </div>
            <div class="d-flex gap-2" style="max-width: 320px; width: 100%;">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" id="tableSearchInput" class="form-control" placeholder="Cari nama tabel..." value="{{ $search ?? '' }}">
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap" id="dbTablesList">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>
                            <a href="{{ route('database-resources.index', ['sort' => 'name', 'dir' => ($sortBy === 'name' && $sortDir === 'asc') ? 'desc' : 'asc', 'q' => $search]) }}" class="text-decoration-none text-dark d-flex align-items-center gap-1">
                                Nama Tabel
                                <i class="bi bi-arrow-down-up text-muted small"></i>
                            </a>
                        </th>
                        <th>Engine</th>
                        <th class="text-end">
                            <a href="{{ route('database-resources.index', ['sort' => 'rows_count', 'dir' => ($sortBy === 'rows_count' && $sortDir === 'desc') ? 'asc' : 'desc', 'q' => $search]) }}" class="text-decoration-none text-dark d-flex align-items-center justify-content-end gap-1">
                                Jumlah Baris
                                <i class="bi bi-arrow-down-up text-muted small"></i>
                            </a>
                        </th>
                        <th class="text-end">Ukuran Data</th>
                        <th class="text-end">Ukuran Indeks</th>
                        <th class="text-end">Overhead (Free)</th>
                        <th class="text-end">
                            <a href="{{ route('database-resources.index', ['sort' => 'total_size', 'dir' => ($sortBy === 'total_size' && $sortDir === 'desc') ? 'asc' : 'desc', 'q' => $search]) }}" class="text-decoration-none text-dark d-flex align-items-center justify-content-end gap-1">
                                Total Ukuran
                                <i class="bi bi-arrow-down-up text-muted small"></i>
                            </a>
                        </th>
                        <th style="width: 120px;">Proporsi</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tables as $index => $t)
                        <tr class="table-row-item" data-name="{{ strtolower($t['name']) }}">
                            <td class="text-muted small">{{ $index + 1 }}</td>
                            <td>
                                <strong class="text-dark">{{ $t['name'] }}</strong>
                                <small class="text-muted d-block">{{ $t['collation'] }}</small>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $t['engine'] }}</span>
                            </td>
                            <td class="text-end fw-semibold">
                                {{ number_format($t['rows_count']) }}
                            </td>
                            <td class="text-end text-muted">
                                {{ $t['data_formatted'] }}
                            </td>
                            <td class="text-end text-muted">
                                {{ $t['index_formatted'] }}
                            </td>
                            <td class="text-end">
                                @if ($t['overhead'] > 0)
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle">
                                        {{ $t['overhead_formatted'] }}
                                    </span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td class="text-end fw-bold text-primary">
                                {{ $t['total_formatted'] }}
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-grow-1" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ max($t['percentage'], 2) }}%"></div>
                                    </div>
                                    <span class="small text-muted" style="width: 42px;">{{ $t['percentage'] }}%</span>
                                </div>
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('database-resources.optimize') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="table" value="{{ $t['name'] }}">
                                    <button type="submit" class="btn btn-outline-primary btn-sm py-1 px-2" title="Optimasi Tabel {{ $t['name'] }}" onclick="return confirm('Jalankan optimasi pada tabel {{ $t['name'] }}?')">
                                        <i class="bi bi-magic me-1"></i> Optimasi
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Tidak ada tabel yang sesuai dengan pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Modal Optimasi Semua Tabel --}}
    <div class="modal fade" id="optimizeAllModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <form method="POST" action="{{ route('database-resources.optimize-all') }}" class="modal-content">
                @csrf
                <div class="modal-header bg-primary text-white">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-tools me-1"></i> Optimasi Seluruh Tabel Database
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-2">
                        Proses ini akan menjalankan perintah <code>OPTIMIZE TABLE</code> pada seluruh <strong>{{ $overview['database']['total_tables'] }} tabel</strong> di database <strong>{{ $overview['database']['name'] }}</strong>.
                    </p>
                    <div class="alert alert-info py-2 px-3 small mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        Optimasi membantu membersihkan fragmentasi penyimpanan data dan menata ulang indeks agar performa query sistem Replay BOS tetap cepat di hosting cPanel.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="bi bi-magic me-1"></i> Mulai Optimasi Semua Tabel
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('tableSearchInput');
        const rows = document.querySelectorAll('.table-row-item');

        if (searchInput) {
            searchInput.addEventListener('input', function () {
                const query = this.value.toLowerCase().trim();
                rows.forEach(function (row) {
                    const tableName = row.getAttribute('data-name') || '';
                    if (!query || tableName.includes(query)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        }
    });
</script>
@endpush
