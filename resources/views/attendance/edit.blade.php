@extends('layouts.app')

@section('title', 'Koreksi Absensi')

@section('content')
    <h5 class="mb-3">Koreksi Absensi — {{ $attendance->employee->name }}, {{ $attendance->date->format('d-m-Y') }}</h5>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-4" style="max-width: 480px;">
        <form method="POST" action="{{ route('attendance.update', $attendance) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Jam Check-in</label>
                <input type="time" name="check_in_at" value="{{ old('check_in_at', $attendance->check_in_at?->format('H:i')) }}" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Jam Check-out</label>
                <input type="time" name="check_out_at" value="{{ old('check_out_at', $attendance->check_out_at?->format('H:i')) }}" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Catatan</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $attendance->notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan Koreksi</button>
            <a href="{{ route('attendance.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
