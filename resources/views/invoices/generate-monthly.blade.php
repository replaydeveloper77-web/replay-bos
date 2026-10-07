@extends('layouts.app')

@section('title', 'Generate Tagihan Bulanan')

@section('content')
    <h5 class="mb-3">Generate Tagihan Bulanan</h5>

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Sistem otomatis generate tagihan ini tiap tanggal 1 jam 01:00. Halaman ini untuk generate manual (mis. kalau ada pelanggan baru yang telat masuk siklus, atau perlu generate ulang bulan lalu).
        Pelanggan dengan Harga Langganan/Bulan terisi &amp; berstatus aktif saat ini: <strong>{{ $billedCount }}</strong> pelanggan.
        Pelanggan yang sudah punya tagihan bulanan untuk periode yang dipilih akan otomatis dilewati (tidak dobel).
    </div>

    <div class="noc-stat-card bg-white p-4" style="max-width: 480px;">
        <form method="POST" action="{{ route('invoices.generate-monthly') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Periode Penagihan</label>
                <input type="month" name="period" value="{{ old('period', now()->format('Y-m')) }}" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-warning" onclick="return confirm('Generate tagihan bulanan untuk periode ini?')">
                <i class="bi bi-receipt"></i> Generate Sekarang
            </button>
            <a href="{{ route('invoices.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
