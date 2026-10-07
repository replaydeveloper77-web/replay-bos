@extends('layouts.app')

@section('title', 'Buat Work Order')

@section('content')
    <h5 class="mb-3">Buat Work Order Instalasi</h5>

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
        <form method="POST" action="{{ route('work-orders.store') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Pelanggan</label>
                <select name="customer_id" class="form-select" required>
                    <option value="">- Pilih Pelanggan -</option>
                    @foreach ($customers as $customer)
                        <option value="{{ $customer->id }}" @selected(old('customer_id', $selectedCustomerId) == $customer->id)>
                            {{ $customer->name }}{{ $customer->activation_status === 'PENDING' ? ' (Menunggu Aktivasi)' : '' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Jenis Pekerjaan</label>
                <select name="type" class="form-select" required>
                    @foreach (\App\Models\WorkOrder::TYPES as $type)
                        <option value="{{ $type }}" @selected(old('type', 'INSTALASI_BARU') === $type)>{{ \App\Models\WorkOrder::typeLabelFor($type) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Tugaskan ke Teknisi (opsional)</label>
                <select name="assigned_to_id" class="form-select">
                    <option value="">- Belum Ditugaskan -</option>
                    @foreach ($technicians as $technician)
                        <option value="{{ $technician->id }}" @selected(old('assigned_to_id') == $technician->id)>{{ $technician->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Atau Tugaskan ke Tim (opsional)</label>
                <select name="team_id" class="form-select">
                    <option value="">- Tidak Ada -</option>
                    @foreach ($teams as $team)
                        <option value="{{ $team->id }}" @selected(old('team_id') == $team->id)>{{ $team->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Tanggal Jadwal (opsional)</label>
                <input type="date" name="scheduled_date" value="{{ old('scheduled_date') }}" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Catatan Penugasan (opsional)</label>
                <textarea name="notes" class="form-control" rows="3">{{ old('notes') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('work-orders.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
