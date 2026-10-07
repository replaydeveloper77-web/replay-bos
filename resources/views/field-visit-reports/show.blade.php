@extends('layouts.app')

@section('title', 'Laporan Kunjungan — ' . $fieldVisitReport->report_number)

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Laporan Kunjungan {{ $fieldVisitReport->report_number }}</h5>
        @can('update', $fieldVisitReport)
            <a href="{{ route('field-visit-reports.edit', $fieldVisitReport) }}" class="btn btn-outline-secondary btn-sm">Edit</a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-4 mb-3">
        <table class="table table-borderless mb-0">
            <tr>
                <th style="width: 200px;">Lokasi</th>
                <td>
                    @if ($fieldVisitReport->customer)
                        <a href="{{ route('customers.show', $fieldVisitReport->customer) }}">{{ $fieldVisitReport->customer->name }}</a>
                    @else
                        {{ $fieldVisitReport->location_label }}
                    @endif
                </td>
            </tr>
            <tr>
                <th>Tujuan Kunjungan</th>
                <td>{{ $fieldVisitReport->purposeLabel() }}</td>
            </tr>
            <tr>
                <th>Tanggal Kunjungan</th>
                <td>{{ $fieldVisitReport->visit_date->format('d-m-Y') }}</td>
            </tr>
            <tr>
                <th>Catatan Kunjungan</th>
                <td>{{ $fieldVisitReport->notes ?? '-' }}</td>
            </tr>
            <tr>
                <th>Hasil / Kesimpulan</th>
                <td>{{ $fieldVisitReport->result ?? '-' }}</td>
            </tr>
            <tr>
                <th>Teknisi</th>
                <td>{{ $fieldVisitReport->createdBy->name }} — {{ $fieldVisitReport->created_at->format('d-m-Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="noc-stat-card bg-white p-3">
        <h6 class="mb-3">Dokumentasi Foto</h6>

        @if ($fieldVisitReport->photos->isEmpty())
            <p class="text-muted small mb-3">Belum ada foto yang diupload.</p>
        @else
            <div class="row g-2 mb-3">
                @foreach ($fieldVisitReport->photos as $photo)
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="border rounded p-1 text-center position-relative">
                            <a href="{{ $photo->url() }}" target="_blank">
                                <img src="{{ $photo->url() }}" class="img-fluid rounded" style="height:90px; object-fit:cover; width:100%;" alt="{{ $photo->original_filename }}">
                            </a>
                            <div class="small text-truncate mt-1" title="{{ $photo->original_filename }}">{{ $photo->original_filename }}</div>
                            <div class="text-muted" style="font-size: 0.7rem;">{{ $photo->uploadedBy?->username }}</div>
                            @can('update', $fieldVisitReport)
                                <form method="POST" action="{{ route('field-visit-reports.photos.destroy', [$fieldVisitReport, $photo]) }}" onsubmit="return confirm('Hapus foto ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 py-0 px-1" style="font-size: 0.7rem;">&times;</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        @can('update', $fieldVisitReport)
            <form method="POST" action="{{ route('field-visit-reports.photos.store', $fieldVisitReport) }}" enctype="multipart/form-data" class="d-flex gap-2">
                @csrf
                <input type="file" name="files[]" multiple accept="image/*" class="form-control form-control-sm">
                <button type="submit" class="btn btn-sm btn-primary text-nowrap">Upload Foto</button>
            </form>
            <div class="form-text">Maks 5 foto sekaligus, masing-masing maks 10MB (jpg/png/gif/webp).</div>
        @endcan
    </div>
@endsection
