@extends('layouts.app')

@section('title', 'Edit Titik Coverage')

@section('content')
    <h5 class="mb-3">Edit Titik Coverage</h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 720px;">
        <form method="POST" action="{{ route('coverage-points.update', $point) }}">
            @csrf
            @method('PUT')
            @include('coverage-points._form')
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('coverage-points.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
