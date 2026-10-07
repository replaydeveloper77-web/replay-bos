@extends('layouts.app')

@section('title', $invoice->invoice_number)

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">
                {{ $invoice->invoice_number }}
                @if ($invoice->isPaid())
                    <span class="badge bg-success">Lunas</span>
                @elseif ($invoice->isOverdue())
                    <span class="badge bg-danger">Telat Bayar</span>
                @else
                    <span class="badge bg-secondary">Belum Lunas</span>
                @endif
            </h5>
            <span class="text-muted small">
                {{ $invoice->typeLabel() }}
                @if ($invoice->quotation)
                    &middot; Dari Surat Penawaran <a href="{{ route('quotations.show', $invoice->quotation) }}">{{ $invoice->quotation->quotation_number }}</a>
                @endif
                @if ($invoice->due_date)
                    &middot; Jatuh tempo {{ $invoice->due_date->format('d/m/Y') }}
                @endif
            </span>
        </div>
        <div>
            <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-sm btn-success">
                <i class="bi bi-file-earmark-pdf"></i> Download Invoice (PDF)
            </a>
            @can('manage', \App\Models\Invoice::class)
                @if ($invoice->isPaid())
                    <form method="POST" action="{{ route('invoices.mark-unpaid', $invoice) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-outline-secondary">Batal Lunas</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('invoices.mark-paid', $invoice) }}" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-warning">Tandai Lunas</button>
                    </form>
                @endif
            @endcan
            <a href="{{ route('invoices.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Pelanggan</h6>
                @if ($invoice->customer)
                    <div class="small">
                        <div><strong>{{ $invoice->customer->name }}</strong> @if($invoice->customer->company) ({{ $invoice->customer->company }}) @endif</div>
                        <div>{{ $invoice->customer->location ?? '-' }}</div>
                        <div>{{ $invoice->customer->whatsapp ?? '-' }}</div>
                        <div class="mt-2">
                            @if ($invoice->customer->isPendingActivation())
                                <span class="badge bg-warning text-dark">Menunggu Aktivasi NOC</span>
                            @else
                                <span class="badge bg-success">Sudah Aktif</span>
                            @endif
                            <a href="{{ route('customers.show', $invoice->customer) }}" class="ms-2">Lihat profil pelanggan &rarr;</a>
                        </div>
                    </div>
                @else
                    <p class="text-muted small mb-0">Data pelanggan tidak ditemukan.</p>
                @endif
            </div>
        </div>
        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Info Invoice</h6>
                <div class="small">
                    <div>Tanggal dibuat: {{ $invoice->created_at->format('d F Y') }}</div>
                    <div>Dibuat oleh: {{ $invoice->createdBy?->name }}</div>
                    @if ($invoice->isPaid())
                        <div>Dilunasi: {{ $invoice->paid_at?->format('d F Y H:i') }} oleh {{ $invoice->paidBy?->name }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="noc-stat-card bg-white p-4 mt-3">
        <h6 class="mb-3">Rincian Harga</h6>
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th class="text-center">Qty</th>
                        <th class="text-end">Harga Satuan</th>
                        <th class="text-end">Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoice->items as $item)
                        <tr>
                            <td>{{ $item->item_name }}</td>
                            <td class="text-center">{{ $item->qty }}</td>
                            <td class="text-end">Rp {{ number_format($item->unit_price, 0, ',', '.') }}</td>
                            <td class="text-end">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="row justify-content-end">
            <div class="col-md-4">
                <table class="table table-sm mb-0">
                    <tr>
                        <td>Subtotal (belum PPN)</td>
                        <td class="text-end">Rp {{ number_format($invoice->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>PPN ({{ rtrim(rtrim(number_format($invoice->ppn_rate, 2, ',', '.'), '0'), ',') }}%)</td>
                        <td class="text-end">Rp {{ number_format($invoice->ppn_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Total (sudah PPN)</td>
                        <td class="text-end">Rp {{ number_format($invoice->total, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
