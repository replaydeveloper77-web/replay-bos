<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Verifikasi 2FA — Replay BOS</title>
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
        .login-card { width: 100%; max-width: 380px; border-radius: .75rem; background: #fff; }
        .btn-noc { background: #2f6fed; border-color: #2f6fed; }
        .btn-noc:hover { background: #2559c4; border-color: #2559c4; }
        .code-input { letter-spacing: .5rem; font-size: 1.5rem; text-align: center; }
    </style>
</head>
<body>
    <div class="login-card shadow p-4 p-md-5">
        <div class="text-center mb-4">
            <i class="bi bi-shield-lock" style="font-size: 2.5rem; color: #2f6fed;"></i>
            <h5 class="mt-2 mb-0">Verifikasi 2 Langkah</h5>
            <small class="text-muted">Masukkan kode 6 digit dari aplikasi authenticator Anda</small>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger py-2">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('two-factor.verify-challenge') }}">
            @csrf
            <div class="mb-3">
                <input type="text" name="code" inputmode="numeric" pattern="[0-9]*" maxlength="6" class="form-control code-input" required autofocus autocomplete="one-time-code">
            </div>
            <button type="submit" class="btn btn-noc w-100 text-white">Verifikasi</button>
        </form>

        <div class="text-center mt-3">
            <a href="{{ route('login') }}" class="small text-muted">&larr; Kembali ke login</a>
        </div>
    </div>
</body>
</html>
