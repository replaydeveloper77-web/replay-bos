@extends('layouts.app')

@section('title', 'Buat Ticket')

@section('content')
    <h5 class="mb-3">Buat Ticket</h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 720px;">
        <form method="POST" action="{{ route('tickets.store') }}">
            @csrf
            @include('tickets._form', ['ticket' => null])
            <button type="submit" class="btn btn-primary">Buat Ticket</button>
            <a href="{{ route('tickets.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
