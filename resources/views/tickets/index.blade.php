@extends('layouts.app')

@section('title', 'Tickets')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
            <h5 class="mb-0">Tickets</h5>
            <div id="app" class="d-inline-flex">
                <ticket-badge></ticket-badge>
            </div>
        </div>
        @can('create', \App\Models\Ticket::class)
            <a href="{{ route('tickets.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Buat Ticket
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Bar Jumlah Per Kategori & Status Tiket --}}
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('tickets.index', array_merge(request()->except(['customer_category', 'page']), request('customer_category') === 'CORPORATE' ? [] : ['customer_category' => 'CORPORATE'])) }}" class="text-decoration-none text-reset d-block h-100">
                <div class="noc-stat-card bg-white p-3 h-100 {{ request('customer_category') === 'CORPORATE' ? 'border border-primary border-2 shadow-sm' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-medium">Tiket Corporate</span>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">CP-</span>
                    </div>
                    <div class="fs-3 fw-bold text-primary my-1">{{ $corporateCount }}</div>
                    <div class="small text-muted d-flex align-items-center justify-content-between">
                        <span>Corporate</span>
                        @if (request('customer_category') === 'CORPORATE')
                            <span class="badge bg-primary text-white" style="font-size: 0.65rem;">Filter Aktif</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('tickets.index', array_merge(request()->except(['customer_category', 'page']), request('customer_category') === 'RESELLER' ? [] : ['customer_category' => 'RESELLER'])) }}" class="text-decoration-none text-reset d-block h-100">
                <div class="noc-stat-card bg-white p-3 h-100 {{ request('customer_category') === 'RESELLER' ? 'border border-warning border-2 shadow-sm' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-medium">Tiket Reseller</span>
                        <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle">RS-</span>
                    </div>
                    <div class="fs-3 fw-bold text-warning my-1">{{ $resellerCount }}</div>
                    <div class="small text-muted d-flex align-items-center justify-content-between">
                        <span>Reseller</span>
                        @if (request('customer_category') === 'RESELLER')
                            <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">Filter Aktif</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('tickets.index', array_merge(request()->except(['customer_category', 'page']), request('customer_category') === 'HOME' ? [] : ['customer_category' => 'HOME'])) }}" class="text-decoration-none text-reset d-block h-100">
                <div class="noc-stat-card bg-white p-3 h-100 {{ request('customer_category') === 'HOME' ? 'border border-info border-2 shadow-sm' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-medium">Tiket Home</span>
                        <span class="badge bg-info-subtle text-info border border-info-subtle">RH-</span>
                    </div>
                    <div class="fs-3 fw-bold text-info my-1">{{ $homeCount }}</div>
                    <div class="small text-muted d-flex align-items-center justify-content-between">
                        <span>Home</span>
                        @if (request('customer_category') === 'HOME')
                            <span class="badge bg-info text-dark" style="font-size: 0.65rem;">Filter Aktif</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('tickets.index', array_merge(request()->except(['status', 'page']), request('status') === 'HANDLE' ? [] : ['status' => 'HANDLE'])) }}" class="text-decoration-none text-reset d-block h-100">
                <div class="noc-stat-card bg-white p-3 h-100 {{ request('status') === 'HANDLE' ? 'border border-primary border-2 shadow-sm' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-medium">Tiket Handle</span>
                        <span class="badge bg-primary text-white">HANDLE</span>
                    </div>
                    <div class="fs-3 fw-bold text-primary my-1">{{ $handleCount }}</div>
                    <div class="small text-muted d-flex align-items-center justify-content-between">
                        <span>Ditangani</span>
                        @if (request('status') === 'HANDLE')
                            <span class="badge bg-primary text-white" style="font-size: 0.65rem;">Filter Aktif</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('tickets.index', array_merge(request()->except(['status', 'page']), request('status') === 'CLOSED' ? [] : ['status' => 'CLOSED'])) }}" class="text-decoration-none text-reset d-block h-100">
                <div class="noc-stat-card bg-white p-3 h-100 {{ request('status') === 'CLOSED' ? 'border border-dark border-2 shadow-sm' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-medium">Tiket Closed</span>
                        <span class="badge bg-dark text-white">CLOSED</span>
                    </div>
                    <div class="fs-3 fw-bold text-secondary my-1">{{ $closedCount }}</div>
                    <div class="small text-muted d-flex align-items-center justify-content-between">
                        <span>Selesai</span>
                        @if (request('status') === 'CLOSED')
                            <span class="badge bg-dark text-white" style="font-size: 0.65rem;">Filter Aktif</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-4 col-lg-2">
            <a href="{{ route('tickets.index', request()->except(['customer_category', 'status', 'page'])) }}" class="text-decoration-none text-reset d-block h-100">
                <div class="noc-stat-card bg-white p-3 h-100 {{ !request('customer_category') && !request('status') ? 'border border-dark-subtle' : '' }}">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-muted small fw-medium">Total Tiket</span>
                        <span class="badge bg-secondary-subtle text-secondary">ALL</span>
                    </div>
                    <div class="fs-3 fw-bold text-dark my-1">{{ $totalCount }}</div>
                    <div class="small text-muted d-flex align-items-center justify-content-between">
                        <span>Semua</span>
                        @if (!request('customer_category') && !request('status'))
                            <span class="badge bg-secondary text-white" style="font-size: 0.65rem;">Semua</span>
                        @endif
                    </div>
                </div>
            </a>
        </div>
    </div>

    {{-- Filter & Pencarian Tiket --}}
    <form method="GET" class="row g-2 mb-3 align-items-center">
        <div class="col-md-3 col-sm-6">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Cari tiket, nama perusahaan...">
            </div>
        </div>
        <div class="col-auto">
            <select name="customer_category" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Kategori (CP/RS/RH)</option>
                <option value="CORPORATE" {{ request('customer_category') === 'CORPORATE' ? 'selected' : '' }}>Corporate (CP)</option>
                <option value="RESELLER" {{ request('customer_category') === 'RESELLER' ? 'selected' : '' }}>Reseller (RS)</option>
                <option value="HOME" {{ request('customer_category') === 'HOME' ? 'selected' : '' }}>Home (RH)</option>
            </select>
        </div>
        <div class="col-auto">
            <select name="category" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Category</option>
                @foreach ($categories as $category)
                    <option value="{{ $category }}" {{ request('category') === $category ? 'selected' : '' }}>
                        {{ \App\Models\Ticket::categoryLabel($category) }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Status</option>
                @foreach ($statuses as $status)
                    <option value="{{ $status }}" {{ request('status') === $status ? 'selected' : '' }}>
                        {{ $status === 'HANDLE' ? 'HANDLE (Sedang Ditangani)' : $status }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="priority" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Priority</option>
                @foreach ($priorities as $priority)
                    <option value="{{ $priority }}" {{ request('priority') === $priority ? 'selected' : '' }}>{{ $priority }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white" title="Filter Tanggal"><i class="bi bi-calendar-event"></i></span>
                <input type="date" name="date" value="{{ request('date') }}" class="form-control" title="Pilih Tanggal Spesifik" onchange="this.form.submit()">
            </div>
        </div>
        <div class="col-auto">
            <select name="month" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Bulan</option>
                @php
                    $monthNames = [
                        1 => 'Januari', 2 => 'Februari', 3 => 'Maret', 4 => 'April',
                        5 => 'Mei', 6 => 'Juni', 7 => 'Juli', 8 => 'Agustus',
                        9 => 'September', 10 => 'Oktober', 11 => 'November', 12 => 'Desember'
                    ];
                @endphp
                @foreach ($monthNames as $mNum => $mLabel)
                    <option value="{{ $mNum }}" {{ (string) request('month') === (string) $mNum ? 'selected' : '' }}>
                        {{ $mLabel }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">Semua Tahun</option>
                @php
                    $yearsList = $availableYears ?? [(int) date('Y')];
                @endphp
                @foreach ($yearsList as $y)
                    <option value="{{ $y }}" {{ (string) request('year') === (string) $y ? 'selected' : '' }}>
                        {{ $y }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-auto d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-search"></i> Cari
            </button>
            @if (request()->hasAny(['search', 'customer_category', 'status', 'priority', 'category', 'date', 'month', 'year']))
                <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                    <i class="bi bi-x-circle"></i> Reset
                </a>
            @endif
        </div>
    </form>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nomor Tiket</th>
                        <th>Nama Tiket / Perusahaan</th>
                        <th>Category</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Waktu SLA</th>
                        <th>Tiket Dibuat</th>
                        <th class="text-center" style="width: 70px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr>
                            <td>
                                <a href="{{ route('tickets.show', $ticket) }}" class="fw-semibold text-decoration-none">{{ $ticket->ticket_number }}</a>
                            </td>
                            <td>{{ $ticket->title }}</td>
                            <td><span class="badge bg-light text-dark border">{{ \App\Models\Ticket::categoryLabel($ticket->category) }}</span></td>
                            <td>@include('tickets._priority_badge')</td>
                            <td>@include('tickets._status_badge')</td>
                            <td>@include('tickets._sla_badge')</td>
                            <td>{{ $ticket->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                @can('delete', $ticket)
                                    <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket {{ $ticket->ticket_number }}? Tindakan ini tidak dapat dibatalkan.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0" title="Hapus Tiket" style="line-height: 1;">
                                            <i class="bi bi-trash fs-5 text-danger"></i>
                                        </button>
                                    </form>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted text-center py-3">Belum ada ticket yang sesuai.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-2">
            <div class="small text-muted">
                @if ($tickets->total() > 0)
                    Menampilkan <strong>{{ $tickets->firstItem() }}</strong> - <strong>{{ $tickets->lastItem() }}</strong> dari <strong>{{ $tickets->total() }}</strong> tiket (10 per halaman)
                @endif
            </div>
            <div>
                {{ $tickets->links() }}
            </div>
        </div>
    </div>
@endsection

