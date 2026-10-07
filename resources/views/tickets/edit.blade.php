@extends('layouts.app')

@section('title', 'Edit Ticket')

@section('content')
    <h5 class="mb-3">Edit Ticket — {{ $ticket->ticket_number }}</h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 720px;">
        <form method="POST" action="{{ route('tickets.update', $ticket) }}">
            @csrf
            @method('PUT')
            @include('tickets._form', ['ticket' => $ticket])
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('tickets.show', $ticket) }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
