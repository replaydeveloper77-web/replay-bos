@extends('layouts.app')

@section('title', 'Edit Paket Layanan')

@section('content')
    <h5 class="mb-3">Edit Paket Layanan</h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('service-packages.update', $package) }}">
            @csrf
            @method('PUT')
            @include('service-packages._form')
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('service-packages.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
