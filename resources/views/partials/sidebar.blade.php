@php
    $user = auth()->user();
@endphp
<aside class="noc-sidebar">
    <div class="brand">
        <img src="{{ asset('images/branding/logo-replay-net.png') }}" alt="Replay">
        <div>
            <strong class="d-block">REPLAY BOS</strong>
            <small>Business Operating System</small>
        </div>
    </div>
    <nav class="nav flex-column py-2">
        @can('dashboard.view')
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2 me-2"></i> Dashboard
            </a>
        @endcan

        <a class="nav-link {{ request()->routeIs('portal-karyawan.*') ? 'active' : '' }}" href="{{ route('portal-karyawan.index') }}">
            <i class="bi bi-person-badge me-2"></i> Portal Karyawan
        </a>

        @can('reseller.portal.view')
            <a class="nav-link {{ request()->routeIs('reseller-portal.*') ? 'active' : '' }}" href="{{ route('reseller-portal.index') }}">
                <i class="bi bi-people me-2"></i> Pelanggan Saya
            </a>
        @endcan

        @can('viewAny', \App\Models\Ticket::class)
            <a class="nav-link {{ request()->routeIs('tickets.*') ? 'active' : '' }}" href="{{ route('tickets.index') }}">
                <i class="bi bi-ticket-perforated me-2"></i> Tickets
            </a>
            <a class="nav-link {{ request()->routeIs('ticket-titles.*') ? 'active' : '' }}" href="{{ route('ticket-titles.index') }}">
                <i class="bi bi-card-heading me-2"></i> Master Judul Tiket
            </a>
        @endcan

        @can('viewAny', \App\Models\Customer::class)
            <a class="nav-link {{ request()->routeIs('customers.*') ? 'active' : '' }}" href="{{ route('customers.index') }}">
                <i class="bi bi-briefcase me-2"></i> Pelanggan
            </a>
        @endcan

        @can('viewAny', \App\Models\Quotation::class)
            <a class="nav-link {{ request()->routeIs('quotations.*') ? 'active' : '' }}" href="{{ route('quotations.index') }}">
                <i class="bi bi-file-earmark-text me-2"></i> Surat Penawaran
            </a>
        @endcan

        @can('viewAny', \App\Models\Invoice::class)
            <a class="nav-link {{ request()->routeIs('invoices.*') ? 'active' : '' }}" href="{{ route('invoices.index') }}">
                <i class="bi bi-receipt me-2"></i> Closing
            </a>
        @endcan

        @can('viewAny', \App\Models\CoveragePoint::class)
            <a class="nav-link {{ request()->routeIs('coverage-points.*') ? 'active' : '' }}" href="{{ route('coverage-points.index') }}">
                <i class="bi bi-geo-alt me-2"></i> Titik Coverage
            </a>
        @endcan

        @can('viewAny', \App\Models\ServicePackage::class)
            <a class="nav-link {{ request()->routeIs('service-packages.*') ? 'active' : '' }}" href="{{ route('service-packages.index') }}">
                <i class="bi bi-box-seam me-2"></i> Paket Layanan
            </a>
        @endcan

        @can('viewAny', \App\Models\Team::class)
            <a class="nav-link {{ request()->routeIs('teams.*') ? 'active' : '' }}" href="{{ route('teams.index') }}">
                <i class="bi bi-diagram-3 me-2"></i> Tim
            </a>
        @endcan

        @can('viewAny', \App\Models\Employee::class)
            <a class="nav-link {{ request()->routeIs('employees.*') ? 'active' : '' }}" href="{{ route('employees.index') }}">
                <i class="bi bi-person-badge me-2"></i> Karyawan
            </a>
        @endcan

        @can('viewAny', \App\Models\Attendance::class)
            <a class="nav-link {{ request()->routeIs('attendance.index') || request()->routeIs('attendance.edit') ? 'active' : '' }}" href="{{ route('attendance.index') }}">
                <i class="bi bi-calendar-check me-2"></i> Laporan Absensi
            </a>
        @endcan

        @can('viewAny', \App\Models\LeaveRequest::class)
            <a class="nav-link {{ request()->routeIs('leave-requests.index') ? 'active' : '' }}" href="{{ route('leave-requests.index') }}">
                <i class="bi bi-calendar-x me-2"></i> Pengajuan Cuti/Izin
            </a>
        @endcan

        @can('contract.template.manage')
            <a class="nav-link {{ request()->routeIs('contract-templates.*') ? 'active' : '' }}" href="{{ route('contract-templates.index') }}">
                <i class="bi bi-file-earmark-text me-2"></i> Redaksi PKWT/PKWTT
            </a>
        @endcan

        @can('viewAny', \App\Models\Asset::class)
            <a class="nav-link {{ request()->routeIs('assets.*') ? 'active' : '' }}" href="{{ route('assets.index') }}">
                <i class="bi bi-box-seam me-2"></i> Aset / Inventaris
            </a>
        @endcan

        @can('viewAny', \App\Models\AssetType::class)
            <a class="nav-link {{ request()->routeIs('asset-types.*') ? 'active' : '' }}" href="{{ route('asset-types.index') }}">
                <i class="bi bi-tags me-2"></i> Jenis Aset
            </a>
        @endcan

        @can('viewAny', \App\Models\WorkOrder::class)
            <a class="nav-link {{ request()->routeIs('work-orders.*') ? 'active' : '' }}" href="{{ route('work-orders.index') }}">
                <i class="bi bi-clipboard-check me-2"></i> Work Order
            </a>
        @endcan

        @can('viewAny', \App\Models\FieldVisitReport::class)
            <a class="nav-link {{ request()->routeIs('field-visit-reports.*') ? 'active' : '' }}" href="{{ route('field-visit-reports.index') }}">
                <i class="bi bi-geo-alt me-2"></i> Laporan Kunjungan
            </a>
        @endcan

        @can('viewAny', \App\Models\User::class)
            <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}">
                <i class="bi bi-people me-2"></i> Users
            </a>
        @endcan

        @can('viewAny', \App\Models\Role::class)
            <a class="nav-link {{ request()->routeIs('roles.*') ? 'active' : '' }}" href="{{ route('roles.index') }}">
                <i class="bi bi-shield-lock me-2"></i> Roles &amp; Permissions
            </a>
        @endcan

        @can('whatsapp.template.manage')
            <a class="nav-link {{ request()->routeIs('whatsapp-templates.*') ? 'active' : '' }}" href="{{ route('whatsapp-templates.index') }}">
                <i class="bi bi-whatsapp me-2"></i> Template WhatsApp
            </a>
        @endcan

        @can('audit.view')
            <a class="nav-link {{ request()->routeIs('audit.*') ? 'active' : '' }}" href="{{ route('audit.index') }}">
                <i class="bi bi-journal-text me-2"></i> Audit Trail
            </a>
        @endcan

        @can('settings.manage')
            <a class="nav-link {{ request()->routeIs('settings.*') ? 'active' : '' }}" href="{{ route('settings.edit') }}">
                <i class="bi bi-gear me-2"></i> Pengaturan
            </a>
        @endcan

        @can('api.manage')
            <a class="nav-link {{ request()->routeIs('api-tokens.*') ? 'active' : '' }}" href="{{ route('api-tokens.index') }}">
                <i class="bi bi-key me-2"></i> API Token
            </a>
        @endcan
    </nav>
</aside>
