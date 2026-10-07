@extends('layouts.app')

@section('title', 'Edit Tim')

@section('content')
    <h5 class="mb-3">Edit Tim — {{ $team->name }}</h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('teams.update', $team) }}">
            @csrf
            @method('PUT')
            @include('teams._form', ['team' => $team])
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('teams.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
