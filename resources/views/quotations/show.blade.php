@extends('layouts.app')

@section('title', $quotation->quotation_number)

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">{{ $quotation->quotation_number }}</h5>
            <span class="badge bg-info-subtle text-info-emphasis">{{ $quotation->status }}</span>
        </div>
        <div>
            <a href="{{ route('quotations.pdf', $quotation) }}" class="btn btn-sm btn-success">
                <i class="bi bi-file-earmark-pdf"></i> Download Surat Penawaran (PDF)
            </a>
            @can('update', $quotation)
                <a href="{{ route('quotations.edit', $quotation) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endcan
            @can('create', \App\Models\Invoice::class)
                @if ($quotation->invoice)
                    <a href="{{ route('invoices.show', $quotation->invoice) }}" class="btn btn-sm btn-dark">
                        <i class="bi bi-receipt"></i> Lihat Invoice
                    </a>
                @else
                    <a href="{{ route('quotations.close-form', $quotation) }}" class="btn btn-sm btn-warning">
                        <i class="bi bi-check2-circle"></i> Proses Closing
                    </a>
                @endif
            @endcan
            <a href="{{ route('quotations.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Data Prospek</h6>
                <div class="row small">
                    <div class="col-6 mb-3">
                        <div class="text-muted">Nama</div>
                        <div>{{ $quotation->prospect_name }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Perusahaan</div>
                        <div>{{ $quotation->prospect_company ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Alamat</div>
                        <div>{{ $quotation->prospect_address ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">WhatsApp</div>
                        <div>{{ $quotation->prospect_phone ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Pelanggan Terdaftar</div>
                        <div>{{ $quotation->customer?->name ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Berlaku Sampai</div>
                        <div>{{ $quotation->valid_until?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                </div>
                @if ($quotation->notes)
                    <div class="mt-2">
                        <div class="text-muted small">Catatan</div>
                        <div class="small">{{ $quotation->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Hasil Cek Coverage</h6>
                @if (is_null($quotation->is_covered))
                    <p class="text-muted small mb-0">Belum ada data lokasi/coverage untuk penawaran ini.</p>
                @elseif ($quotation->is_covered)
                    <div class="alert alert-success mb-0">
                        <i class="bi bi-check-circle"></i> Tercover — jarak {{ $quotation->nearest_distance_km }} km dari {{ $quotation->nearest_point_name }}.
                    </div>
                @else
                    <div class="alert alert-danger mb-0">
                        <i class="bi bi-x-circle"></i> Belum tercover — titik terdekat {{ $quotation->nearest_point_name }} berjarak {{ $quotation->nearest_distance_km }} km.
                    </div>
                @endif
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
                    @foreach ($quotation->items as $item)
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
                        <td class="text-end">Rp {{ number_format($quotation->subtotal, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>PPN ({{ rtrim(rtrim(number_format($quotation->ppn_rate, 2, ',', '.'), '0'), ',') }}%)</td>
                        <td class="text-end">Rp {{ number_format($quotation->ppn_amount, 0, ',', '.') }}</td>
                    </tr>
                    <tr class="fw-bold">
                        <td>Total (sudah PPN)</td>
                        <td class="text-end">Rp {{ number_format($quotation->total, 0, ',', '.') }}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
@endsection
