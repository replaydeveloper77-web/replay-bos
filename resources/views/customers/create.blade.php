@extends('layouts.app')

@section('title', 'Tambah Pelanggan')

@section('content')
    <h5 class="mb-3">Tambah Pelanggan</h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            @include('customers._form', ['customer' => null])
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('customers.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
