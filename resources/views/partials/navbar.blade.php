<header class="noc-topbar d-flex align-items-center justify-content-between px-4 py-3">
    <div class="d-flex align-items-center">
        <button id="noc-sidebar-toggle" class="btn btn-sm btn-outline-secondary d-lg-none me-3">
            <i class="bi bi-list"></i>
        </button>
        <h5 class="mb-0">@yield('title', 'Dashboard')</h5>
    </div>
    <div class="dropdown">
        <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle me-1"></i> {{ auth()->user()->name }}
        </button>
        <ul class="dropdown-menu dropdown-menu-end">
            <li><span class="dropdown-item-text text-muted small">{{ auth()->user()->username }}</span></li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a href="{{ route('password.edit') }}" class="dropdown-item">
                    <i class="bi bi-key me-1"></i> Ganti Password
                </a>
            </li>
            @if (config('auth.two_factor_enabled', true))
            <li>
                <a href="{{ route('two-factor.setup') }}" class="dropdown-item">
                    <i class="bi bi-shield-lock me-1"></i> Keamanan Akun (2FA)
                    @if (auth()->user()->hasTwoFactorEnabled())
                        <span class="badge bg-success ms-1">Aktif</span>
                    @endif
                </a>
            </li>
            @endif
            <li><hr class="dropdown-divider"></li>
            <li>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="dropdown-item">
                        <i class="bi bi-box-arrow-right me-1"></i> Logout
                    </button>
                </form>
            </li>
        </ul>
    </div>
</header>
