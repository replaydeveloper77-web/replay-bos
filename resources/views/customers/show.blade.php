@extends('layouts.app')

@section('title', $customer->name)

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">{{ $customer->name }}</h5>
            <span class="badge bg-secondary">{{ ucfirst(strtolower($customer->category)) }}</span>
            @if ($customer->is_active)
                <span class="badge bg-success">Aktif</span>
            @else
                <span class="badge bg-danger">Nonaktif</span>
            @endif
            @if ($customer->isPendingActivation())
                <span class="badge bg-warning text-dark">Menunggu Aktivasi NOC</span>
            @endif
        </div>
        <div>
            @can('create', \App\Models\WorkOrder::class)
                @if ($customer->isPendingActivation())
                    <a href="{{ route('work-orders.create', ['customer_id' => $customer->id]) }}" class="btn btn-sm btn-outline-warning">
                        <i class="bi bi-clipboard-check"></i> Buat Work Order
                    </a>
                @endif
            @endcan
            @can('activate', \App\Models\Customer::class)
                @if ($customer->isPendingActivation())
                    <form method="POST" action="{{ route('customers.activate', $customer) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-warning" onclick="return confirm('Tandai pelanggan ini sudah selesai diaktivasi?')">
                            <i class="bi bi-check2-circle"></i> Tandai Aktivasi Selesai
                        </button>
                    </form>
                @endif
            @endcan
            @can('update', \App\Models\Customer::class)
                <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endcan
            @can('delete', \App\Models\Customer::class)
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan {{ $customer->name }}? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-danger">
                        <i class="bi bi-trash"></i> Hapus
                    </button>
                </form>
            @endcan
            <a href="{{ route('customers.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Informasi Pelanggan</h6>
                <div class="row small">
                    <div class="col-6 mb-3">
                        <div class="text-muted">Perusahaan</div>
                        <div>{{ $customer->company ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Nama PIC</div>
                        <div>{{ $customer->pic_name ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">No. WhatsApp</div>
                        <div>{{ $customer->whatsapp ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Email</div>
                        <div>{{ $customer->email ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Lokasi</div>
                        <div>{{ $customer->location ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Layanan</div>
                        <div>{{ $customer->service ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Nomor Layanan</div>
                        <div><code>{{ $customer->service_number ?? '-' }}</code></div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Dibuat oleh</div>
                        <div>{{ $customer->createdBy?->username }} — {{ $customer->created_at->format('d/m/Y') }}</div>
                    </div>
                    @if ($customer->category === 'HOME')
                        <div class="col-6 mb-3">
                            <div class="text-muted">Reseller Induk</div>
                            <div>
                                @if ($customer->reseller)
                                    <a href="{{ route('customers.show', $customer->reseller) }}">{{ $customer->reseller->name }}</a>
                                @else
                                    Langsung ke Replay (tanpa reseller)
                                @endif
                            </div>
                        </div>
                    @endif
                    @can('viewAny', \App\Models\Invoice::class)
                        <div class="col-6 mb-3">
                            <div class="text-muted">Paket Langganan</div>
                            <div>{{ $customer->servicePackage?->name ?? '-' }}</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-muted">Harga Langganan/Bulan</div>
                            <div>{{ $customer->hasBilling() ? 'Rp '.number_format($customer->subscription_price, 0, ',', '.') : '-' }}</div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="text-muted">Jatuh Tempo</div>
                            <div>Tanggal {{ $customer->billing_due_day }} tiap bulan</div>
                        </div>
                    @endcan
                </div>
                @if ($customer->notes)
                    <div class="mt-2">
                        <div class="text-muted small">Catatan</div>
                        <div class="small">{{ $customer->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Alokasi IP</h6>
                @if ($customer->ipAllocations->isEmpty())
                    <p class="text-muted small mb-0">Belum ada IP yang dialokasikan.</p>
                @else
                    <table class="table table-sm mb-0">
                        <thead>
                            <tr>
                                <th>IP Address</th>
                                <th>Label</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($customer->ipAllocations as $allocation)
                                <tr>
                                    <td><code>{{ $allocation->ip_address }}</code></td>
                                    <td>{{ $allocation->label ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>

    <div class="noc-stat-card bg-white p-4 mt-3">
        <h6 class="mb-3">Riwayat Ticket ({{ $tickets->count() }})</h6>
        @if ($tickets->isEmpty())
            <p class="text-muted small mb-0">Pelanggan ini belum pernah punya ticket.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle">
                    <thead>
                        <tr>
                            <th>Ticket</th>
                            <th>Judul</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>PIC</th>
                            <th>Dibuat</th>
                            <th>Hasil Penanganan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td><a href="{{ route('tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                                <td>{{ $ticket->title }}</td>
                                <td>@include('tickets._priority_badge')</td>
                                <td>@include('tickets._status_badge')</td>
                                <td>{{ $ticket->pic?->name ?? '-' }}</td>
                                <td>{{ $ticket->created_at->format('d/m/Y') }}</td>
                                <td class="small text-muted">{{ \Illuminate\Support\Str::limit($ticket->resolution_note, 60) ?: '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    @can('viewAny', \App\Models\WorkOrder::class)
        <div class="noc-stat-card bg-white p-4 mt-3">
            <h6 class="mb-3">Riwayat Work Order ({{ $workOrders->count() }})</h6>
            @if ($workOrders->isEmpty())
                <p class="text-muted small mb-0">Pelanggan ini belum pernah punya work order.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>No. WO</th>
                                <th>Jenis</th>
                                <th>Ditugaskan ke</th>
                                <th>Jadwal</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($workOrders as $wo)
                                <tr>
                                    <td><a href="{{ route('work-orders.show', $wo) }}">{{ $wo->work_order_number }}</a></td>
                                    <td>{{ $wo->typeLabel() }}</td>
                                    <td>{{ $wo->assignedTo->name ?? ($wo->team->name ?? '-') }}</td>
                                    <td>{{ $wo->scheduled_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td>{{ $wo->statusLabel() }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endcan

    @if ($customer->category === 'RESELLER')
        <div class="noc-stat-card bg-white p-4 mt-3">
            <h6 class="mb-3">Pelanggan Home di Bawah Reseller Ini ({{ $homeCustomers->count() }})</h6>
            @if ($homeCustomers->isEmpty())
                <p class="text-muted small mb-0">Belum ada pelanggan Home yang terdaftar di bawah reseller ini.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>WhatsApp</th>
                                <th>Lokasi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($homeCustomers as $home)
                                <tr>
                                    <td><a href="{{ route('customers.show', $home) }}">{{ $home->name }}</a></td>
                                    <td>{{ $home->whatsapp ?? '-' }}</td>
                                    <td>{{ $home->location ?? '-' }}</td>
                                    <td>
                                        @if ($home->is_active)
                                            <span class="badge bg-success">Aktif</span>
                                        @else
                                            <span class="badge bg-danger">Nonaktif</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    @can('viewAny', \App\Models\Invoice::class)
        <div class="noc-stat-card bg-white p-4 mt-3">
            <h6 class="mb-3">Riwayat Invoice ({{ $invoices->count() }})</h6>
            @if ($invoices->isEmpty())
                <p class="text-muted small mb-0">Pelanggan ini belum pernah punya invoice.</p>
            @else
                <div class="table-responsive">
                    <table class="table table-sm align-middle">
                        <thead>
                            <tr>
                                <th>No. Invoice</th>
                                <th>Jenis</th>
                                <th>Jatuh Tempo</th>
                                <th class="text-end">Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($invoices as $invoice)
                                <tr>
                                    <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                                    <td>{{ $invoice->typeLabel() }}</td>
                                    <td>{{ $invoice->due_date?->format('d/m/Y') ?? '-' }}</td>
                                    <td class="text-end">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
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
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endcan
@endsection
