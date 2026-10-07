@extends('layouts.app')

@section('title', 'Edit Laporan Kunjungan')

@section('content')
    <h5 class="mb-3">Edit Laporan Kunjungan — {{ $fieldVisitReport->report_number }}</h5>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('field-visit-reports.update', $fieldVisitReport) }}">
            @csrf
            @method('PUT')
            @include('field-visit-reports._form')
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('field-visit-reports.show', $fieldVisitReport) }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
