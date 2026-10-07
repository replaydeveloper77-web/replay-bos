@extends('layouts.app')

@section('title', 'Laporan Kunjungan Lapangan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Laporan Kunjungan Lapangan</h5>
        @can('create', \App\Models\FieldVisitReport::class)
            <a href="{{ route('field-visit-reports.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Buat Laporan
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-3 mb-3">
        <form method="GET" class="row g-2">
            <div class="col-md-6">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nomor laporan / pelanggan / lokasi...">
            </div>
            <div class="col-md-4">
                <select name="purpose" class="form-select">
                    <option value="">- Semua Tujuan -</option>
                    @foreach (\App\Models\FieldVisitReport::PURPOSES as $purpose)
                        <option value="{{ $purpose }}" @selected(request('purpose') === $purpose)>{{ \App\Models\FieldVisitReport::purposeLabelFor($purpose) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>No. Laporan</th>
                        <th>Tanggal</th>
                        <th>Lokasi</th>
                        <th>Tujuan</th>
                        <th>Teknisi</th>
                        <th style="width: 90px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($reports as $report)
                        <tr>
                            <td>{{ $report->report_number }}</td>
                            <td>{{ $report->visit_date->format('d-m-Y') }}</td>
                            <td>{{ $report->locationName() }}</td>
                            <td>{{ $report->purposeLabel() }}</td>
                            <td>{{ $report->createdBy->name }}</td>
                            <td>
                                <a href="{{ route('field-visit-reports.show', $report) }}" class="btn btn-sm btn-outline-secondary">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-3">Belum ada laporan kunjungan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $reports->withQueryString()->links() }}
    </div>
@endsection
