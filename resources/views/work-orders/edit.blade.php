@extends('layouts.app')

@section('title', 'Edit Work Order')

@section('content')
    <h5 class="mb-3">Edit Work Order — {{ $workOrder->work_order_number }}</h5>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-4" style="max-width: 640px;">
        <div class="mb-3">
            <label class="form-label">Pelanggan</label>
            <input type="text" class="form-control" value="{{ $workOrder->customer->name }}" disabled>
        </div>
        <div class="mb-3">
            <label class="form-label">Jenis Pekerjaan</label>
            <input type="text" class="form-control" value="{{ $workOrder->typeLabel() }}" disabled>
        </div>
        <form method="POST" action="{{ route('work-orders.update', $workOrder) }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Tugaskan ke Teknisi</label>
                <select name="assigned_to_id" class="form-select">
                    <option value="">- Belum Ditugaskan -</option>
                    @foreach ($technicians as $technician)
                        <option value="{{ $technician->id }}" @selected(old('assigned_to_id', $workOrder->assigned_to_id) == $technician->id)>{{ $technician->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Atau Tugaskan ke Tim</label>
                <select name="team_id" class="form-select">
                    <option value="">- Tidak Ada -</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @selected(old('team_id', $workOrder->team_id) == $team->id)>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Jadwal</label>
                <input type="date" name="scheduled_date" value="{{ old('scheduled_date', $workOrder->scheduled_date?->format('Y-m-d')) }}" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Catatan Penugasan</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes', $workOrder->notes) }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('work-orders.show', $workOrder) }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
