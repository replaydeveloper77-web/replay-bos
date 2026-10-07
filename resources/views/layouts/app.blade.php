<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') — Replay BOS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('images/branding/favicon-replay-net.png') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --noc-navy: #0b1d3a;
            --noc-navy-dark: #071228;
            --noc-blue: #2f6fed;
            --noc-gray: #6b7785;
            --noc-border: #1c2f52;
        }
        body { background: #f2f4f7; }
        .noc-sidebar {
            width: 250px;
            min-height: 100vh;
            background: var(--noc-navy);
            color: #cfd8ea;
            position: fixed;
            top: 0; left: 0; bottom: 0;
        }
        .noc-sidebar .brand {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--noc-border);
            display: flex;
            align-items: center;
            gap: .65rem;
        }
        .noc-sidebar .brand img { width: 36px; height: 36px; flex-shrink: 0; }
        .noc-sidebar .brand strong { color: #fff; font-size: 1.05rem; }
        .noc-sidebar .brand small { color: var(--noc-gray); }
        .noc-sidebar .nav-link {
            color: #cfd8ea;
            padding: .6rem 1.25rem;
            border-left: 3px solid transparent;
        }
        .noc-sidebar .nav-link:hover { background: rgba(255,255,255,.05); color: #fff; }
        .noc-sidebar .nav-link.active {
            background: rgba(47,111,237,.15);
            border-left-color: var(--noc-blue);
            color: #fff;
        }
        .noc-content { margin-left: 250px; }
        .noc-topbar {
            background: #fff;
            border-bottom: 1px solid #e3e7ee;
        }
        .noc-stat-card { border: 1px solid #e3e7ee; border-radius: .6rem; }
        @media (max-width: 991.98px) {
            .noc-sidebar { transform: translateX(-100%); transition: transform .2s ease; z-index: 1040; }
            .noc-sidebar.show { transform: translateX(0); }
            .noc-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @include('partials.sidebar')

    <div class="noc-content">
        @include('partials.navbar')

        <main class="p-4">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('noc-sidebar-toggle')?.addEventListener('click', function () {
            document.querySelector('.noc-sidebar').classList.toggle('show');
        });
    </script>
    @stack('scripts')
</body>
</html>
