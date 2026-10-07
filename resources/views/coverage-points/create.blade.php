@extends('layouts.app')

@section('title', 'Tambah Titik Coverage')

@section('content')
    <h5 class="mb-3">Tambah Titik Coverage (POP/Tower)</h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 720px;">
        <form method="POST" action="{{ route('coverage-points.store') }}">
            @csrf
            @include('coverage-points._form', ['point' => null])
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('coverage-points.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
