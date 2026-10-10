@extends('layouts.app')

@section('title', 'Upgrade / Downgrade Paket')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="mb-0 fw-bold">
                <i class="bi bi-arrow-down-up text-primary me-2"></i>Permohonan Upgrade / Downgrade Paket
            </h5>
            <small class="text-muted">Pengajuan perubahan paket layanan pelanggan ke Finance. Persetujuan Finance otomatis men-generate Work Order upgrade.</small>
        </div>
        @can('create', \App\Models\PackageChangeRequest::class)
            <a href="{{ route('package-changes.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg me-1"></i> Ajukan Upgrade/Downgrade
            </a>
        @endcan
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

    {{-- Stat Cards --}}
    <div class="row g-3 mb-3">
        <div class="col-6 col-md-3">
            <div class="noc-stat-card bg-white p-3 h-100 border-start border-4 border-primary">
                <small class="text-muted d-block mb-1">Total Pengajuan</small>
                <h4 class="mb-0 fw-bold text-dark">{{ number_format($stats['total'] ?? 0) }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="noc-stat-card bg-white p-3 h-100 border-start border-4 border-warning">
                <small class="text-muted d-block mb-1">Menunggu Finance</small>
                <h4 class="mb-0 fw-bold text-warning">{{ number_format($stats['pending'] ?? 0) }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="noc-stat-card bg-white p-3 h-100 border-start border-4 border-success">
                <small class="text-muted d-block mb-1">Disetujui</small>
                <h4 class="mb-0 fw-bold text-success">{{ number_format($stats['approved'] ?? 0) }}</h4>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="noc-stat-card bg-white p-3 h-100 border-start border-4 border-danger">
                <small class="text-muted d-block mb-1">Ditolak</small>
                <h4 class="mb-0 fw-bold text-danger">{{ number_format($stats['rejected'] ?? 0) }}</h4>
            </div>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="noc-stat-card bg-white p-3 mb-3">
        <form method="GET" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-light text-muted"><i class="bi bi-search"></i></span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari no. pengajuan / pelanggan / no. WO...">
                </div>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">- Semua Status -</option>
                    @foreach (\App\Models\PackageChangeRequest::STATUSES as $st)
                        <option value="{{ $st }}" @selected(request('status') === $st)>{{ match($st) {
                            'PENDING' => 'Menunggu Finance',
                            'APPROVED' => 'Disetujui',
                            'REJECTED' => 'Ditolak',
                            'CANCELLED' => 'Dibatalkan',
                            default => $st
                        } }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="type" class="form-select">
                    <option value="">- Semua Jenis -</option>
                    @foreach (\App\Models\PackageChangeRequest::TYPES as $tp)
                        <option value="{{ $tp }}" @selected(request('type') === $tp)>{{ match($tp) {
                            'UPGRADE' => 'Upgrade Paket',
                            'DOWNGRADE' => 'Downgrade Paket',
                            default => $tp
                        } }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
                @if (request()->hasAny(['q', 'status', 'type']))
                    <a href="{{ route('package-changes.index') }}" class="btn btn-outline-secondary" title="Reset"><i class="bi bi-x-lg"></i></a>
                @endif
            </div>
        </form>
    </div>

    {{-- Table List --}}
    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>No. Pengajuan</th>
                        <th>Pelanggan</th>
                        <th>Jenis</th>
                        <th>Perubahan Paket</th>
                        <th>Biaya Baru</th>
                        <th>Status</th>
                        <th>Nomor WO</th>
                        <th>Diajukan</th>
                        <th class="text-end" style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($packageChanges as $item)
                        <tr>
                            <td>
                                <a href="{{ route('package-changes.show', $item) }}" class="fw-bold text-decoration-none">
                                    {{ $item->request_number }}
                                </a>
                                <small class="d-block text-muted">{{ $item->created_at->format('d/m/Y H:i') }}</small>
                            </td>
                            <td>
                                <span class="fw-semibold d-block">{{ $item->customer->name }}</span>
                                <small class="text-muted">
                                    {{ $item->customer->service_number ?? 'No ID Layanan' }}
                                </small>
                            </td>
                            <td>
                                <span class="badge {{ $item->typeBadgeClass() }}">
                                    @if ($item->type === 'UPGRADE')
                                        <i class="bi bi-arrow-up-circle me-1"></i> Upgrade
                                    @else
                                        <i class="bi bi-arrow-down-circle me-1"></i> Downgrade
                                    @endif
                                </span>
                            </td>
                            <td>
                                <div class="small">
                                    <span class="text-muted">{{ $item->currentPackageLabel() }}</span>
                                    <i class="bi bi-arrow-right mx-1 text-primary"></i>
                                    <strong class="text-dark">{{ $item->targetPackageLabel() }}</strong>
                                </div>
                            </td>
                            <td>
                                @if ($item->target_price !== null)
                                    <span class="fw-bold text-primary">
                                        Rp {{ number_format($item->target_price, 0, ',', '.') }}
                                    </span>
                                    @if ($item->current_price !== null)
                                        <small class="d-block text-muted text-decoration-line-through">
                                            Rp {{ number_format($item->current_price, 0, ',', '.') }}
                                        </small>
                                    @endif
                                @else
                                    <span class="text-muted small">Ditetapkan Finance</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $item->statusBadgeClass() }}">
                                    {{ $item->statusLabel() }}
                                </span>
                            </td>
                            <td>
                                @if ($item->workOrder)
                                    <a href="{{ route('work-orders.show', $item->workOrder) }}" class="badge bg-primary text-decoration-none" title="Klik untuk lihat Work Order">
                                        <i class="bi bi-clipboard-check me-1"></i> {{ $item->workOrder->work_order_number }}
                                    </a>
                                @elseif ($item->isApproved())
                                    <span class="text-muted small">Auto-generated</span>
                                @else
                                    <span class="text-muted small">-</span>
                                @endif
                            </td>
                            <td>
                                <small class="d-block">{{ $item->requestedBy->name }}</small>
                            </td>
                            <td class="text-end">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('package-changes.show', $item) }}" class="btn btn-outline-secondary" title="Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    @if ($item->isPending())
                                        @can('approve', $item)
                                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $item->id }}" title="Setujui (Approve & Generate WO)">
                                                <i class="bi bi-check-lg"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $item->id }}" title="Tolak">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        @endcan

                                        @can('cancel', $item)
                                            <button type="button" class="btn btn-outline-secondary text-danger" data-bs-toggle="modal" data-bs-target="#cancelModal{{ $item->id }}" title="Batalkan Pengajuan">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        @endcan
                                    @endif
                                </div>

                                {{-- Approve Modal --}}
                                @can('approve', $item)
                                    <div class="modal fade text-start" id="approveModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="POST" action="{{ route('package-changes.approve', $item) }}" class="modal-content">
                                                @csrf
                                                <div class="modal-header bg-success text-white">
                                                    <h6 class="modal-title fw-bold">
                                                        <i class="bi bi-check-circle me-1"></i> Setujui {{ $item->typeLabel() }}
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-2">
                                                        Apakah Anda yakin ingin menyetujui pengajuan <strong>{{ $item->request_number }}</strong> untuk pelanggan <strong>{{ $item->customer->name }}</strong>?
                                                    </p>
                                                    <div class="alert alert-info py-2 px-3 small mb-3">
                                                        <i class="bi bi-info-circle me-1"></i> Setelah disetujui, sistem akan <strong>men-generate nomor Work Order Upgrade secara otomatis</strong> dan memperbarui paket pelanggan ke <strong>{{ $item->targetPackageLabel() }}</strong>.
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-semibold">Biaya Langganan Baru per Bulan (Rp - Opsional):</label>
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text">Rp</span>
                                                            <input type="number" step="0.01" min="0" name="new_price" class="form-control" value="{{ $item->target_price }}" placeholder="Kosongkan jika tidak ada tarif khusus...">
                                                        </div>
                                                    </div>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-semibold">Catatan Finance (Opsional):</label>
                                                        <textarea name="finance_notes" class="form-control form-control-sm" rows="3" placeholder="Contoh: Disetujui, tarif tagihan bulan depan disesuaikan."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-success btn-sm">
                                                        <i class="bi bi-check-lg me-1"></i> Setujui &amp; Generate WO
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>

                                    {{-- Reject Modal --}}
                                    <div class="modal fade text-start" id="rejectModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="POST" action="{{ route('package-changes.reject', $item) }}" class="modal-content">
                                                @csrf
                                                <div class="modal-header bg-danger text-white">
                                                    <h6 class="modal-title fw-bold">
                                                        <i class="bi bi-x-circle me-1"></i> Tolak {{ $item->typeLabel() }}
                                                    </h6>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-2">
                                                        Tolak pengajuan perubahan paket <strong>{{ $item->request_number }}</strong>?
                                                    </p>
                                                    <div class="mb-2">
                                                        <label class="form-label small fw-semibold text-danger">Alasan Penolakan Finance (Wajib):</label>
                                                        <textarea name="finance_notes" class="form-control form-control-sm" rows="3" required placeholder="Contoh: Pelanggan masih memiliki tunggakan tagihan bulan lalu."></textarea>
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

                                {{-- Cancel Modal --}}
                                @can('cancel', $item)
                                    <div class="modal fade text-start" id="cancelModal{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog">
                                            <form method="POST" action="{{ route('package-changes.cancel', $item) }}" class="modal-content">
                                                @csrf
                                                <div class="modal-header">
                                                    <h6 class="modal-title fw-bold text-danger">Batalkan Pengajuan</h6>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body">
                                                    Apakah Anda yakin ingin membatalkan pengajuan <strong>{{ $item->request_number }}</strong>?
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Kembali</button>
                                                    <button type="submit" class="btn btn-danger btn-sm">Ya, Batalkan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                                Belum ada permohonan upgrade / downgrade paket.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($packageChanges->hasPages())
            <div class="mt-3">
                {{ $packageChanges->links() }}
            </div>
        @endif
    </div>
@endsection
