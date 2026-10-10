@extends('layouts.app')

@section('title', 'Detail Permohonan ' . $packageChange->request_number)

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <div class="d-flex align-items-center gap-2">
                <h5 class="mb-0 fw-bold">{{ $packageChange->request_number }}</h5>
                <span class="badge {{ $packageChange->typeBadgeClass() }}">
                    {{ $packageChange->typeLabel() }}
                </span>
                <span class="badge {{ $packageChange->statusBadgeClass() }}">
                    {{ $packageChange->statusLabel() }}
                </span>
            </div>
            <small class="text-muted">Diajukan pada {{ $packageChange->created_at->format('d F Y, H:i') }} WIB oleh {{ $packageChange->requestedBy->name }}</small>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('package-changes.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
            </a>

            @if ($packageChange->isPending())
                @can('approve', $packageChange)
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#approveModal">
                        <i class="bi bi-check-lg me-1"></i> Setujui &amp; Generate WO
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
                    </button>
                @endcan

                @can('cancel', $packageChange)
                    <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#cancelModal">
                        Batalkan
                    </button>
                @endcan
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Alert Success Work Order Auto-Generated --}}
    @if ($packageChange->isApproved() && $packageChange->workOrder)
        <div class="alert alert-success d-flex align-items-center justify-content-between p-3 mb-3 border-start border-4 border-success">
            <div>
                <h6 class="alert-heading fw-bold mb-1">
                    <i class="bi bi-clipboard-check-fill me-1"></i> Nomor Work Order Otomatis: {{ $packageChange->workOrder->work_order_number }}
                </h6>
                <div class="small">
                    Pengajuan telah disetujui Finance. Work Order otomatis digenerate dan siap ditugaskan kepada tim teknisi lapangan.
                </div>
            </div>
            <a href="{{ route('work-orders.show', $packageChange->workOrder) }}" class="btn btn-success btn-sm text-nowrap ms-2">
                <i class="bi bi-box-arrow-up-right me-1"></i> Buka Work Order
            </a>
        </div>
    @endif

    <div class="row g-3">
        {{-- Left Column: Comparison & Details --}}
        <div class="col-lg-8">
            {{-- Package Comparison Card --}}
            <div class="noc-stat-card bg-white p-4 mb-3">
                <h6 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-arrow-left-right text-primary me-2"></i>Perbandingan Paket Layanan
                </h6>
                <div class="row g-3 align-items-center">
                    <div class="col-md-5">
                        <div class="p-3 bg-light rounded text-center">
                            <span class="badge bg-secondary mb-2">Paket Lama</span>
                            <h6 class="fw-bold text-dark mb-1">{{ $packageChange->currentPackageLabel() }}</h6>
                            <div class="text-muted small mb-2">{{ $packageChange->currentPackage?->speed_label ?? '-' }}</div>
                            @if ($packageChange->current_price !== null)
                                <div class="fs-5 fw-bold text-muted text-decoration-line-through">
                                    Rp {{ number_format($packageChange->current_price, 0, ',', '.') }}<small class="fs-6 text-muted">/bln</small>
                                </div>
                            @else
                                <div class="text-muted small">-</div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-2 text-center text-primary fs-3">
                        <i class="bi bi-arrow-right-circle-fill d-none d-md-inline"></i>
                        <i class="bi bi-arrow-down-circle-fill d-inline d-md-none"></i>
                    </div>
                    <div class="col-md-5">
                        <div class="p-3 bg-primary-subtle border border-primary-subtle rounded text-center">
                            <span class="badge bg-primary mb-2">Paket Baru Tujuan</span>
                            <h6 class="fw-bold text-primary mb-1">{{ $packageChange->targetPackageLabel() }}</h6>
                            <div class="text-muted small mb-2">{{ $packageChange->targetPackage?->speed_label ?? 'Kustom' }}</div>
                            @if ($packageChange->target_price !== null)
                                <div class="fs-5 fw-bold text-primary">
                                    Rp {{ number_format($packageChange->target_price, 0, ',', '.') }}<small class="fs-6 text-muted">/bln</small>
                                </div>
                            @else
                                <div class="text-muted small fst-italic">Ditetapkan Finance</div>
                            @endif
                        </div>
                    </div>
                </div>

                @if ($packageChange->notes)
                    <div class="mt-3 pt-3 border-top">
                        <label class="small text-muted fw-semibold">Catatan Pengajuan:</label>
                        <p class="mb-0 text-dark small bg-light p-2 rounded">{{ $packageChange->notes }}</p>
                    </div>
                @endif
            </div>

            {{-- Finance Approval Card --}}
            <div class="noc-stat-card bg-white p-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-cash-stack text-success me-2"></i>Status Persetujuan Finance
                </h6>
                <div class="row g-3">
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Status:</small>
                        <span class="badge {{ $packageChange->statusBadgeClass() }} fs-6">
                            {{ $packageChange->statusLabel() }}
                        </span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Diverifikasi Oleh:</small>
                        <strong class="text-dark">{{ $packageChange->approvedBy?->name ?? '-' }}</strong>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Waktu Persetujuan:</small>
                        <span>{{ $packageChange->approved_at ? $packageChange->approved_at->format('d F Y, H:i') . ' WIB' : '-' }}</span>
                    </div>
                    <div class="col-sm-6">
                        <small class="text-muted d-block">Work Order Terkait:</small>
                        @if ($packageChange->workOrder)
                            <a href="{{ route('work-orders.show', $packageChange->workOrder) }}" class="fw-bold text-decoration-none">
                                <i class="bi bi-clipboard-check me-1"></i> {{ $packageChange->workOrder->work_order_number }}
                            </a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </div>
                </div>

                @if ($packageChange->finance_notes)
                    <div class="mt-3 pt-3 border-top">
                        <label class="small text-muted fw-semibold">Catatan dari Finance:</label>
                        <div class="alert alert-secondary py-2 px-3 small mb-0">
                            {{ $packageChange->finance_notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Right Column: Customer Info --}}
        <div class="col-lg-4">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="fw-bold mb-3 border-bottom pb-2">
                    <i class="bi bi-person-badge text-info me-2"></i>Informasi Pelanggan
                </h6>
                <div class="mb-2">
                    <small class="text-muted d-block">Nama Pelanggan:</small>
                    <a href="{{ route('customers.show', $packageChange->customer) }}" class="fw-bold text-decoration-none">
                        {{ $packageChange->customer->name }}
                    </a>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Nomor ID Layanan:</small>
                    <code>{{ $packageChange->customer->service_number ?? '-' }}</code>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Kategori:</small>
                    <span class="badge bg-light text-dark border">{{ $packageChange->customer->category ?? 'Home' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">No. WhatsApp / Kontak:</small>
                    <span>{{ $packageChange->customer->whatsapp ?? '-' }}</span>
                </div>
                <div class="mb-2">
                    <small class="text-muted d-block">Lokasi / Alamat:</small>
                    <small class="text-muted">{{ $packageChange->customer->location ?? '-' }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    @if ($packageChange->isPending())
        @can('approve', $packageChange)
            {{-- Approve Modal --}}
            <div class="modal fade" id="approveModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('package-changes.approve', $packageChange) }}" class="modal-content">
                        @csrf
                        <div class="modal-header bg-success text-white">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-check-circle me-1"></i> Konfirmasi Persetujuan Finance
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2">
                                Anda akan menyetujui pengajuan <strong>{{ $packageChange->request_number }}</strong> untuk pelanggan <strong>{{ $packageChange->customer->name }}</strong>.
                            </p>
                            <div class="alert alert-info py-2 px-3 small mb-3">
                                <i class="bi bi-gear-fill me-1"></i> Sistem akan secara otomatis:
                                <ul class="mb-0 ps-3 mt-1">
                                    <li>Men-generate nomor <strong>Work Order Upgrade</strong>.</li>
                                    <li>Memperbarui paket pelanggan ke <strong>{{ $packageChange->targetPackageLabel() }}</strong>.</li>
                                    <li>Memperbarui tarif tagihan bulanan pada master data pelanggan.</li>
                                </ul>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-semibold">Biaya Langganan Baru per Bulan (Rp - Opsional):</label>
                                <div class="input-group">
                                    <span class="input-group-text">Rp</span>
                                    <input type="number" step="0.01" min="0" name="new_price" class="form-control" value="{{ $packageChange->target_price }}" placeholder="Biaya langganan baru...">
                                </div>
                                <div class="form-text small">Dapat diisi untuk menetapkan nominal tagihan bulanan pelanggan.</div>
                            </div>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold">Catatan Finance (Opsional):</label>
                                <textarea name="finance_notes" class="form-control" rows="3" placeholder="Contoh: Disetujui, tagihan bulan berjalan prorate/penyesuaian."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-success btn-sm">
                                <i class="bi bi-check-lg me-1"></i> Ya, Setujui &amp; Generate WO
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Reject Modal --}}
            <div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('package-changes.reject', $packageChange) }}" class="modal-content">
                        @csrf
                        <div class="modal-header bg-danger text-white">
                            <h6 class="modal-title fw-bold">
                                <i class="bi bi-x-circle me-1"></i> Tolak Permohonan
                            </h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="mb-2">
                                Yakin ingin menolak permohonan <strong>{{ $packageChange->request_number }}</strong>?
                            </p>
                            <div class="mb-2">
                                <label class="form-label small fw-semibold text-danger">Alasan Penolakan Finance (Wajib):</label>
                                <textarea name="finance_notes" class="form-control" rows="3" required placeholder="Tulis alasan penolakan..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-danger btn-sm">
                                <i class="bi bi-x-lg me-1"></i> Tolak Pengajuan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan

        @can('cancel', $packageChange)
            <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog">
                    <form method="POST" action="{{ route('package-changes.cancel', $packageChange) }}" class="modal-content">
                        @csrf
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold text-danger">Batalkan Pengajuan</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            Yakin ingin membatalkan pengajuan <strong>{{ $packageChange->request_number }}</strong>?
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Kembali</button>
                            <button type="submit" class="btn btn-danger btn-sm">Ya, Batalkan</button>
                        </div>
                    </form>
                </div>
            </div>
        @endcan
    @endif
@endsection
