@extends('layouts.app')

@section('title', 'Edit Master Judul Tiket')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Edit Master Judul Tiket</h5>
        <a href="{{ route('ticket-titles.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <div class="noc-stat-card bg-white p-4" style="max-width: 680px;">
        <form method="POST" action="{{ route('ticket-titles.update', $title) }}">
            @csrf
            @method('PUT')
            @include('ticket-titles._form', ['title' => $title])
            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                <a href="{{ route('ticket-titles.index') }}" class="btn btn-outline-secondary ms-2">Batal</a>
            </div>
        </form>
    </div>
@endsection
