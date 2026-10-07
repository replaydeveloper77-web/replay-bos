@extends('layouts.app')

@section('title', 'API Token')

@section('content')
    <h5 class="mb-3">API Token</h5>

    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Token ini dipakai buat layanan lain (misal nanti RADIUS atau Payment Gateway) supaya bisa memanggil API REPLAY BOS dengan aman. Bukan buat login user biasa.
    </div>

    @if (session('plainTextToken'))
        <div class="alert alert-warning">
            <strong>Token baru berhasil dibuat — copy sekarang, tidak akan ditampilkan lagi:</strong>
            <div class="mt-2">
                <code id="new-token" class="user-select-all">{{ session('plainTextToken') }}</code>
                <button type="button" class="btn btn-sm btn-outline-secondary ms-2" onclick="navigator.clipboard.writeText(document.getElementById('new-token').textContent)">Copy</button>
            </div>
        </div>
    @endif

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-4 mb-3" style="max-width: 480px;">
        <h6 class="mb-3">Buat Token Baru</h6>
        <form method="POST" action="{{ route('api-tokens.store') }}" class="d-flex gap-2">
            @csrf
            <input type="text" name="name" class="form-control" placeholder="mis. RADIUS Server, Payment Gateway" required>
            <button type="submit" class="btn btn-primary text-nowrap">Buat Token</button>
        </form>
    </div>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Dibuat</th>
                        <th>Terakhir Dipakai</th>
                        <th style="width: 100px;"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tokens as $token)
                        <tr>
                            <td>{{ $token->name }}</td>
                            <td>{{ $token->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $token->last_used_at?->format('d/m/Y H:i') ?? 'Belum pernah' }}</td>
                            <td>
                                <form method="POST" action="{{ route('api-tokens.destroy', $token) }}" onsubmit="return confirm('Cabut token ini? Layanan yang pakai token ini akan langsung berhenti bisa akses.')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">Cabut</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-muted text-center py-3">Belum ada token.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
