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
    @vite(['resources/css/app.css', 'resources/js/app.ts'])
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
            height: 100vh;
            max-height: 100vh;
            background: var(--noc-navy);
            color: #cfd8ea;
            position: fixed;
            top: 0; left: 0; bottom: 0;
            overflow-y: auto;
            overflow-x: hidden;
            overscroll-behavior: contain;
            -webkit-overflow-scrolling: touch;
            display: flex;
            flex-direction: column;
            z-index: 1040;
            scrollbar-width: thin;
            scrollbar-color: rgba(255, 255, 255, 0.25) transparent;
        }
        .noc-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .noc-sidebar::-webkit-scrollbar-track {
            background: transparent;
        }
        .noc-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.25);
            border-radius: 4px;
        }
        .noc-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.45);
        }
        .noc-sidebar .brand {
            position: sticky;
            top: 0;
            background: var(--noc-navy);
            z-index: 2;
            padding: 1rem 1.25rem;
            border-bottom: 1px solid var(--noc-border);
            display: flex;
            align-items: center;
            gap: .65rem;
            flex-shrink: 0;
        }
        .noc-sidebar .nav {
            padding-bottom: 2.5rem;
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
        .noc-sidebar-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(7, 18, 40, 0.65);
            backdrop-filter: blur(2px);
            z-index: 1035;
        }
        @media (max-width: 991.98px) {
            .noc-sidebar {
                transform: translateX(-100%);
                transition: transform .25s cubic-bezier(0.4, 0, 0.2, 1);
                z-index: 1040;
                box-shadow: none;
            }
            .noc-sidebar.show {
                transform: translateX(0);
                box-shadow: 0 0 35px rgba(0, 0, 0, 0.55);
            }
            .noc-sidebar-backdrop.show {
                display: block;
            }
            .noc-content { margin-left: 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
    <div id="noc-sidebar-backdrop" class="noc-sidebar-backdrop"></div>
    @include('partials.sidebar')

    <div class="noc-content">
        @include('partials.navbar')

        <main class="p-4">
            @yield('content')
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const sidebar = document.querySelector('.noc-sidebar');
        const backdrop = document.getElementById('noc-sidebar-backdrop');
        const toggleBtn = document.getElementById('noc-sidebar-toggle');

        function openSidebar() {
            sidebar?.classList.add('show');
            backdrop?.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar?.classList.remove('show');
            backdrop?.classList.remove('show');
            document.body.style.overflow = '';
        }

        toggleBtn?.addEventListener('click', function (e) {
            e.stopPropagation();
            if (sidebar?.classList.contains('show')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });

        backdrop?.addEventListener('click', closeSidebar);

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && sidebar?.classList.contains('show')) {
                closeSidebar();
            }
        });

        document.querySelectorAll('.noc-sidebar .nav-link').forEach(link => {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
