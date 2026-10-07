@extends('layouts.app')

@section('title', 'Ganti Password')

@section('content')
    <h5 class="mb-3">Ganti Password</h5>

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

    <div class="noc-stat-card bg-white p-4" style="max-width: 480px;">
        <form method="POST" action="{{ route('password.update') }}">
            @csrf
            @method('PUT')
            <div class="mb-3">
                <label class="form-label">Password Saat Ini</label>
                <input type="password" name="current_password" class="form-control" required autocomplete="current-password">
            </div>
            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="new_password" class="form-control" required minlength="8" autocomplete="new-password">
                <div class="form-text">Minimal 8 karakter.</div>
            </div>
            <div class="mb-3">
                <label class="form-label">Ulangi Password Baru</label>
                <input type="password" name="new_password_confirmation" class="form-control" required autocomplete="new-password">
            </div>
            <button type="submit" class="btn btn-primary w-100">Simpan Password Baru</button>
        </form>
    </div>
@endsection
