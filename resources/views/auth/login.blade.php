<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login — Replay BOS</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/favicon-replay-net.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(160deg, #071228 0%, #0b1d3a 55%, #123061 100%);
        }
        .login-card {
            width: 100%;
            max-width: 380px;
            border-radius: .75rem;
            border: 1px solid rgba(255,255,255,.08);
            background: #fff;
        }
        .login-brand strong { color: #0b1d3a; }
        .login-brand small { color: #6b7785; }
        .btn-noc { background: #2f6fed; border-color: #2f6fed; }
        .btn-noc:hover { background: #2559c4; border-color: #2559c4; }
    </style>
</head>
<body>
    <div class="login-card shadow p-4 p-md-5">
        <div class="login-brand text-center mb-4">
            <img src="{{ asset('images/branding/logo-replay-net.png') }}" alt="Replay" style="width: 72px; height: 72px;" class="mb-2">
            <strong class="d-block fs-4">REPLAY BOS</strong>
            <small>Business Operating System &middot; PT Replay Inti Media</small>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" value="{{ old('username') }}" class="form-control" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-check mb-3">
                <input type="checkbox" name="remember" class="form-check-input" id="remember">
                <label class="form-check-label" for="remember">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-noc w-100 text-white">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login
            </button>
        </form>
    </div>
</body>
</html>
