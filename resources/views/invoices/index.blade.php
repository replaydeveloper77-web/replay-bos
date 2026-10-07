@extends('layouts.app')

@section('title', 'Closing / Invoice')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Closing &amp; Invoice</h5>
        <div>
            @can('manage', \App\Models\Invoice::class)
                <a href="{{ route('invoices.generate-monthly-form') }}" class="btn btn-warning btn-sm">
                    <i class="bi bi-receipt"></i> Generate Tagihan Bulanan
                </a>
            @endcan
            @can('create', \App\Models\Invoice::class)
                <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-lg"></i> Buat Invoice Manual
                </a>
            @endcan
        </div>
    </div>

    <ul class="nav nav-pills mb-3">
        <li class="nav-item"><a class="nav-link {{ ! request('status') ? 'active' : '' }}" href="{{ route('invoices.index') }}">Semua</a></li>
        <li class="nav-item"><a class="nav-link {{ request('status') === 'UNPAID' ? 'active' : '' }}" href="{{ route('invoices.index', ['status' => 'UNPAID']) }}">Belum Lunas</a></li>
        <li class="nav-item"><a class="nav-link {{ request('status') === 'OVERDUE' ? 'active' : '' }}" href="{{ route('invoices.index', ['status' => 'OVERDUE']) }}">Telat Bayar</a></li>
        <li class="nav-item"><a class="nav-link {{ request('status') === 'PAID' ? 'active' : '' }}" href="{{ route('invoices.index', ['status' => 'PAID']) }}">Lunas</a></li>
    </ul>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>No. Invoice</th>
                        <th>Pelanggan</th>
                        <th>Jenis</th>
                        <th>Jatuh Tempo</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th style="width: 160px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($invoices as $invoice)
                        <tr>
                            <td><a href="{{ route('invoices.show', $invoice) }}">{{ $invoice->invoice_number }}</a></td>
                            <td>
                                @if ($invoice->customer)
                                    <a href="{{ route('customers.show', $invoice->customer) }}">{{ $invoice->customer->name }}</a>
                                    @if ($invoice->customer->isPendingActivation())
                                        <span class="badge bg-warning text-dark">Menunggu Aktivasi</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>{{ $invoice->typeLabel() }}</td>
                            <td>{{ $invoice->due_date?->format('d/m/Y') ?? '-' }}</td>
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
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-sm btn-outline-secondary">Lihat</a>
                                    @can('manage', \App\Models\Invoice::class)
                                        @if ($invoice->isPaid())
                                            <form method="POST" action="{{ route('invoices.mark-unpaid', $invoice) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-secondary">Batal Lunas</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}">
                                                @csrf
                                                <button class="btn btn-sm btn-success">Tandai Lunas</button>
                                            </form>
                                        @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-muted text-center py-3">Belum ada invoice.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $invoices->links() }}
    </div>
@endsection
