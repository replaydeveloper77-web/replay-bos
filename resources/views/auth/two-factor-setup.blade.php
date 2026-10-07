@extends('layouts.app')

@section('title', 'Keamanan Akun — 2FA')

@section('content')
    <h5 class="mb-3">Keamanan Akun — Autentikasi 2 Faktor (2FA)</h5>

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

    @if (auth()->user()->hasTwoFactorEnabled())
        <div class="noc-stat-card bg-white p-4" style="max-width: 480px;">
            <div class="alert alert-success"><i class="bi bi-check-circle"></i> 2FA aktif di akun Anda.</div>
            <p class="small text-muted">Kalau ganti HP atau kehilangan akses ke aplikasi authenticator, nonaktifkan dulu di sini lalu aktifkan ulang dengan HP yang baru.</p>
            <form method="POST" action="{{ route('two-factor.disable') }}">
                @csrf
                @method('DELETE')
                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-outline-danger" onclick="return confirm('Nonaktifkan 2FA? Akun jadi kurang aman sampai diaktifkan lagi.')">Nonaktifkan 2FA</button>
            </form>
        </div>
    @else
        @if ($forced)
            <div class="alert alert-warning">
                <i class="bi bi-exclamation-triangle"></i> Role Anda (SuperAdmin/Finance) WAJIB pakai 2FA. Selesaikan setup di bawah ini dulu sebelum bisa lanjut pakai aplikasi.
            </div>
        @endif

        <div class="noc-stat-card bg-white p-4" style="max-width: 480px;">
            <ol class="small">
                <li>Install aplikasi authenticator (Google Authenticator, Authy, dll) di HP.</li>
                <li>Scan QR code di bawah ini (atau masukkan kode manual).</li>
                <li>Masukkan kode 6 digit yang muncul di aplikasi authenticator buat konfirmasi.</li>
            </ol>

            <div class="text-center my-3">
                {!! $qrSvg !!}
            </div>

            <div class="mb-3">
                <div class="text-muted small">Kode manual (kalau tidak bisa scan QR)</div>
                <code class="user-select-all">{{ $secret }}</code>
            </div>

            <form method="POST" action="{{ route('two-factor.confirm') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Kode Konfirmasi</label>
                    <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" class="form-control" required autofocus>
                </div>
                <button type="submit" class="btn btn-primary">Aktifkan 2FA</button>
            </form>
        </div>
    @endif
@endsection
