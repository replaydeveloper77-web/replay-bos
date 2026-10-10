@extends('layouts.app')

@section('title', 'Portal Teknisi')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-tools text-primary me-2"></i>Portal Teknisi Lapangan
            </h5>
            <small class="text-muted">Pusat penanganan tugas Work Order, tiket perbaikan, dan laporan kunjungan teknisi</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @can('create', \App\Models\FieldVisitReport::class)
                <a href="{{ route('field-visit-reports.create') }}" class="btn btn-sm btn-success">
                    <i class="bi bi-plus-circle me-1"></i> Buat Laporan Kunjungan
                </a>
            @endcan
            @can('viewAny', \App\Models\WorkOrder::class)
                <a href="{{ route('work-orders.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-card-checklist me-1"></i> Semua Work Order
                </a>
            @endcan
            @can('viewAny', \App\Models\Ticket::class)
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-ticket-perforated me-1"></i> Semua Tiket
                </a>
            @endcan
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Banner Profil Teknisi --}}
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
        <div class="p-3 p-md-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0369a1 100%); color: #fff;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center bg-white text-primary shadow-sm" style="width: 58px; height: 58px; font-size: 1.6rem;">
                        @if ($employee && $employee->photo_path)
                            <img src="{{ $employee->photoUrl() }}" alt="{{ $user->name }}" class="rounded-circle" style="width: 58px; height: 58px; object-fit: cover;">
                        @else
                            <i class="bi bi-person-gear"></i>
                        @endif
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-white">{{ $user->name }}</h5>
                        <div class="small text-light opacity-75 d-flex flex-wrap gap-2 align-items-center">
                            <span>
                                <i class="bi bi-people me-1"></i>Tim:
                                @if ($myTeams->isNotEmpty())
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $myTeams->pluck('name')->join(', ') }}</span>
                                @else
                                    <span class="text-white-50">Belum terhubung ke tim</span>
                                @endif
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Status Kehadiran Hari Ini --}}
                <div class="bg-white bg-opacity-10 border border-white border-opacity-25 rounded-3 p-2 px-3 text-white">
                    <div class="small text-white-50">Presensi Hari Ini ({{ now()->translatedFormat('d M Y') }}):</div>
                    @if ($todayAttendance && $todayAttendance->isCheckedIn())
                        <div class="fw-semibold text-success d-flex align-items-center gap-1 mt-1">
                            <i class="bi bi-check-circle-fill"></i> Sudah Check-In ({{ $todayAttendance->check_in_at?->format('H:i') }} WIB)
                        </div>
                    @else
                        <div class="d-flex align-items-center justify-content-between gap-2 mt-1">
                            <span class="small text-warning"><i class="bi bi-clock-history me-1"></i>Belum Check-In</span>
                            <a href="{{ route('portal-karyawan.index') }}" class="btn btn-xs btn-light py-0 px-2 small text-dark" style="font-size: 0.75rem;">
                                Absen Sekarang &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Kartu Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3 border-start border-4 border-warning shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">WO Terjadwal</div>
                        <div class="fs-4 fw-bold text-dark">{{ $stats['scheduled_wo'] }}</div>
                    </div>
                    <div class="text-warning fs-3"><i class="bi bi-calendar-check"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3 border-start border-4 border-primary shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">WO In Progress</div>
                        <div class="fs-4 fw-bold text-primary">{{ $stats['in_progress_wo'] }}</div>
                    </div>
                    <div class="text-primary fs-3"><i class="bi bi-wrench-adjustable-circle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3 border-start border-4 border-danger shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Tiket Aktif</div>
                        <div class="fs-4 fw-bold text-danger">{{ $stats['active_tickets'] }}</div>
                    </div>
                    <div class="text-danger fs-3"><i class="bi bi-exclamation-triangle"></i></div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3 border-start border-4 border-success shadow-sm">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small">Kunjungan Saya</div>
                        <div class="fs-4 fw-bold text-success">{{ $stats['my_visits'] }}</div>
                    </div>
                    <div class="text-success fs-3"><i class="bi bi-geo-alt-fill"></i></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        {{-- Kolom Kiri: Work Orders & Tiket Aktif --}}
        <div class="col-lg-8">
            {{-- Tabel Work Order Aktif --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-card-checklist text-primary me-2"></i>Tugas Work Order Aktif (Ditugaskan)
                    </h6>
                    <span class="badge bg-primary">{{ $activeWorkOrders->count() }} Tugas</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th>No. Work Order</th>
                                    <th>Pelanggan</th>
                                    <th>Jenis Tugas</th>
                                    <th>Jadwal</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activeWorkOrders as $wo)
                                    <tr>
                                        <td>
                                            <a href="{{ route('work-orders.show', $wo) }}" class="fw-bold text-decoration-none font-monospace">
                                                {{ $wo->work_order_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $wo->customer?->name ?? '-' }}</div>
                                            <small class="text-muted font-monospace">{{ $wo->customer?->service_number ?? '' }}</small>
                                        </td>
                                        <td>
                                            @if ($wo->type === 'INSTALASI_BARU')
                                                <span class="badge bg-info-subtle text-info border border-info-subtle">Instalasi Baru</span>
                                            @elseif ($wo->type === 'RELOKASI')
                                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle">Relokasi</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">{{ $wo->type }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $wo->scheduled_date?->format('d/m/Y') ?? '-' }}</div>
                                        </td>
                                        <td>
                                            @if ($wo->status === 'SCHEDULED')
                                                <span class="badge bg-warning text-dark"><i class="bi bi-clock me-1"></i>Terjadwal</span>
                                            @elseif ($wo->status === 'IN_PROGRESS')
                                                <span class="badge bg-primary"><i class="bi bi-gear-fill me-1"></i>In Progress</span>
                                            @elseif ($wo->status === 'DONE')
                                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Selesai</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $wo->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('work-orders.show', $wo) }}" class="btn btn-sm btn-outline-primary py-1 px-2">
                                                <i class="bi bi-eye me-1"></i>Detail
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4 text-muted">
                                            <i class="bi bi-clipboard-check fs-2 d-block mb-1 text-secondary opacity-50"></i>
                                            Tidak ada Work Order aktif yang sedang ditugaskan kepada Anda atau tim Anda saat ini.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- Tabel Tiket Gangguan / Pekerjaan Lapangan --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-ticket-perforated text-danger me-2"></i>Tiket Gangguan / Perbaikan Aktif
                    </h6>
                    <span class="badge bg-danger">{{ $activeTickets->count() }} Tiket</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-nowrap">
                            <thead class="table-light small text-secondary">
                                <tr>
                                    <th>Nomor Tiket</th>
                                    <th>Pelanggan</th>
                                    <th>Gangguan</th>
                                    <th>Status</th>
                                    <th class="text-end">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($activeTickets as $ticket)
                                    <tr>
                                        <td>
                                            <a href="{{ route('tickets.show', $ticket) }}" class="fw-bold font-monospace text-decoration-none">
                                                {{ $ticket->ticket_number }}
                                            </a>
                                        </td>
                                        <td>
                                            <div class="fw-semibold">{{ $ticket->customer?->name ?? '-' }}</div>
                                            <small class="text-muted">{{ $ticket->customer?->service ?? '-' }}</small>
                                        </td>
                                        <td>
                                            <span class="d-inline-block text-truncate" style="max-width: 200px;">
                                                {{ $ticket->title }}
                                            </span>
                                        </td>
                                        <td>
                                            @if ($ticket->status === 'OPEN')
                                                <span class="badge bg-danger">Open</span>
                                            @elseif ($ticket->status === 'HANDLE')
                                                <span class="badge bg-primary">Handle</span>
                                            @elseif ($ticket->status === 'PENDING')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif ($ticket->status === 'ESCALATED')
                                                <span class="badge bg-info text-dark">Escalated</span>
                                            @else
                                                <span class="badge bg-secondary">{{ $ticket->status }}</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-sm btn-outline-danger py-1 px-2">
                                                <i class="bi bi-arrow-right-circle me-1"></i>Buka Tiket
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted">
                                            <i class="bi bi-check2-circle fs-2 d-block mb-1 text-success opacity-50"></i>
                                            Tidak ada tiket gangguan aktif yang menunggu tindakan.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Kolom Kanan: Laporan Kunjungan Terbaru & Pintasan --}}
        <div class="col-lg-4">
            {{-- Laporan Kunjungan Terbaru --}}
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-geo-alt text-success me-2"></i>Kunjungan Terbaru
                    </h6>
                    @can('create', \App\Models\FieldVisitReport::class)
                        <a href="{{ route('field-visit-reports.create') }}" class="btn btn-xs btn-outline-success py-1 px-2 small">
                            <i class="bi bi-plus"></i> Buat
                        </a>
                    @endcan
                </div>
                <div class="card-body p-3">
                    @forelse ($recentVisits as $visit)
                        <div class="p-2 mb-2 border rounded bg-light">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <a href="{{ route('field-visit-reports.show', $visit) }}" class="fw-bold font-monospace small text-decoration-none">
                                    {{ $visit->report_number }}
                                </a>
                                <small class="text-muted">{{ $visit->visit_date?->format('d/m/Y') }}</small>
                            </div>
                            <div class="small fw-semibold text-dark">{{ $visit->locationName() }}</div>
                            <div class="small text-muted d-flex justify-content-between align-items-center mt-1">
                                <span><i class="bi bi-tag me-1"></i>{{ $visit->purposeLabel() }}</span>
                                <a href="{{ route('field-visit-reports.show', $visit) }}" class="text-primary small text-decoration-none">Detail &rarr;</a>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted small">
                            <i class="bi bi-geo opacity-50 fs-3 d-block mb-1"></i>
                            Belum ada laporan kunjungan yang dibuat.
                        </div>
                    @endforelse
                </div>
            </div>

            {{-- Pintasan Operasional Teknisi --}}
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3 border-bottom">
                    <h6 class="mb-0 fw-bold">
                        <i class="bi bi-lightning-charge text-warning me-2"></i>Pintasan Mandiri Teknisi
                    </h6>
                </div>
                <div class="list-group list-group-flush small">
                    <a href="{{ route('portal-karyawan.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3">
                        <span><i class="bi bi-person-badge text-primary me-2"></i>Portal Karyawan &amp; Absensi</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    <a href="{{ route('leave-requests.self') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3">
                        <span><i class="bi bi-calendar2-range text-info me-2"></i>Pengajuan Cuti / Izin</span>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                    @can('viewAny', \App\Models\Asset::class)
                        <a href="{{ route('assets.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3">
                            <span><i class="bi bi-box-seam text-secondary me-2"></i>Inventaris &amp; Aset Modem/ONT</span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    @endcan
                    @can('viewAny', \App\Models\CoveragePoint::class)
                        <a href="{{ route('coverage-points.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center py-2 px-3">
                            <span><i class="bi bi-pin-map text-danger me-2"></i>Cek Titik Coverage Area</span>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </a>
                    @endcan
                </div>
            </div>
        </div>
    </div>
@endsection
