@extends('layouts.app')

@section('title', 'Tambah Tim')

@section('content')
    <h5 class="mb-3">Tambah Tim</h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('teams.store') }}">
            @csrf
            @include('teams._form', ['team' => null])
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
