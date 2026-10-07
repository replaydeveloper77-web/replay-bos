@extends('layouts.app')

@section('title', 'Tambah Master Judul Tiket')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Tambah Master Judul Tiket</h5>
        <a href="{{ route('ticket-titles.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="noc-stat-card bg-white p-4" style="max-width: 680px;">
        <form method="POST" action="{{ route('ticket-titles.store') }}">
            @csrf
            @include('ticket-titles._form', ['title' => null])
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan Judul Tiket</button>
                <a href="{{ route('ticket-titles.index') }}" class="btn btn-outline-secondary ms-2">Batal</a>
            </div>
        </form>
    </div>
@endsection
