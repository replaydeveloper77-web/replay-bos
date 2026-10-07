@extends('layouts.app')

@section('title', 'Surat Penawaran')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Surat Penawaran Harga</h5>
        @can('create', \App\Models\Quotation::class)
            <a href="{{ route('quotations.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Buat Penawaran
            </a>
        @endcan
    </div>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>No. Penawaran</th>
                        <th>Prospek</th>
                        <th>Coverage</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Dibuat oleh</th>
                        <th>Tanggal</th>
                        <th style="width: 90px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($quotations as $q)
                        <tr>
                            <td><a href="{{ route('quotations.show', $q) }}">{{ $q->quotation_number }}</a></td>
                            <td>{{ $q->prospect_name }} @if($q->prospect_company)<div class="small text-muted">{{ $q->prospect_company }}</div>@endif</td>
                            <td>
                                @if (is_null($q->is_covered))
                                    <span class="badge bg-secondary">Belum dicek</span>
                                @elseif ($q->is_covered)
                                    <span class="badge bg-success">Tercover</span>
                                @else
                                    <span class="badge bg-danger">Di luar coverage</span>
                                @endif
                            </td>
                            <td>Rp {{ number_format($q->total, 0, ',', '.') }}</td>
                            <td><span class="badge bg-info-subtle text-info-emphasis">{{ $q->status }}</span></td>
                            <td>{{ $q->createdBy?->name }}</td>
                            <td>{{ $q->created_at->format('d/m/Y') }}</td>
                            <td><a href="{{ route('quotations.show', $q) }}" class="btn btn-sm btn-outline-secondary">Lihat</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-muted text-center py-3">Belum ada surat penawaran.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $quotations->links() }}
    </div>
@endsection
