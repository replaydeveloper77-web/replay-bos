@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($canViewTickets)
        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-lg-3">
                <div class="noc-stat-card bg-white p-3">
                    <div class="text-muted small">Open Tickets</div>
                    <div class="fs-3 fw-semibold">{{ $openTickets }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="noc-stat-card bg-white p-3">
                    <div class="text-muted small">Critical (Open)</div>
                    <div class="fs-3 fw-semibold text-danger">{{ $criticalTickets }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="noc-stat-card bg-white p-3">
                    <div class="text-muted small">Escalated</div>
                    <div class="fs-3 fw-semibold text-warning">{{ $escalatedTickets }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-3">
                <div class="noc-stat-card bg-white p-3">
                    <div class="text-muted small">SLA Breach</div>
                    <div class="fs-3 fw-semibold text-danger">{{ $slaBreached }}</div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3">
                <div class="text-muted small">Active Users</div>
                <div class="fs-3 fw-semibold">{{ $activeUsers }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3">
                <div class="text-muted small">Logins Today</div>
                <div class="fs-3 fw-semibold text-success">{{ $loginsToday }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3">
                <div class="text-muted small">Failed Logins Today</div>
                <div class="fs-3 fw-semibold text-danger">{{ $failedLoginsToday }}</div>
            </div>
        </div>
        <div class="col-sm-6 col-lg-3">
            <div class="noc-stat-card bg-white p-3">
                <div class="text-muted small">Your Role</div>
                <div class="fs-6 fw-semibold pt-2">
                    @foreach (auth()->user()->roles as $role)
                        <span class="badge bg-primary">{{ $role->name }}</span>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    @if ($canViewInvoices)
        <div class="row g-3 mb-3">
            <div class="col-sm-6 col-lg-4">
                <div class="noc-stat-card bg-white p-3">
                    <div class="text-muted small">Invoice Belum Lunas</div>
                    <div class="fs-3 fw-semibold">{{ $unpaidInvoices }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="noc-stat-card bg-white p-3">
                    <div class="text-muted small">Telat Bayar</div>
                    <div class="fs-3 fw-semibold text-danger">{{ $overdueInvoices }}</div>
                </div>
            </div>
            <div class="col-sm-6 col-lg-4">
                <div class="noc-stat-card bg-white p-3">
                    <div class="text-muted small">Total Belum Tertagih</div>
                    <div class="fs-5 fw-semibold">Rp {{ number_format($unpaidTotal, 0, ',', '.') }}</div>
                </div>
            </div>
        </div>

        <div class="noc-stat-card bg-white p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Invoice Terbaru</h6>
                <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Pelanggan</th>
                            <th>Jenis</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentInvoices as $invoice)
                            <tr>
                                <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                                <td>{{ $invoice->customer?->name ?? '-' }}</td>
                                <td>{{ $invoice->typeLabel() }}</td>
                                <td>Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                                <td>
                                    @if ($invoice->isPaid())
                                        <span class="badge bg-success">Lunas</span>
                                    @elseif ($invoice->isOverdue())
                                        <span class="badge bg-danger">Telat Bayar</span>
                                    @else
                                        <span class="badge bg-secondary">Belum Lunas</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center py-3">Belum ada invoice.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    @if ($canViewTickets)
        <div class="noc-stat-card bg-white p-3 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0">Recent Tickets</h6>
                <a href="{{ route('tickets.index') }}" class="btn btn-sm btn-outline-secondary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Judul</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>PIC</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($recentTickets as $ticket)
                            <tr>
                                <td><a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                                <td>{{ $ticket->title }}</td>
                                <td>@include('tickets._priority_badge')</td>
                                <td>@include('tickets._status_badge')</td>
                                <td>{{ $ticket->pic?->name ?? '-' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="text-muted text-center py-3">Belum ada ticket.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-3">
        <h6 class="mb-3">Recent Login Activity</h6>
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <thead>
                    <tr>
                        <th>Waktu</th>
                        <th>Username</th>
                        <th>IP</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($recentLogins as $log)
                        <tr>
                            <td>{{ $log->created_at->format('d/m/Y H:i:s') }}</td>
                            <td>{{ $log->username_attempted }}</td>
                            <td>{{ $log->ip_address }}</td>
                            <td>
                                @if ($log->status === 'SUCCESS')
                                    <span class="badge bg-success">SUCCESS</span>
                                @elseif ($log->status === 'FAILED')
                                    <span class="badge bg-danger">FAILED</span>
                                @else
                                    <span class="badge bg-secondary">LOGOUT</span>
                                @endif
                            </td>
                            <td>{{ $log->failure_reason }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-muted text-center py-3">Belum ada aktivitas login.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
