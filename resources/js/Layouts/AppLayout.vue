<script setup lang="ts">
import { ref, computed } from 'vue';
import { usePage, Link, router } from '@inertiajs/vue3';
import type { PageProps } from '@/types';

defineProps<{
    title?: string;
}>();

const page = usePage<PageProps>();
const user = computed(() => page.props.auth?.user);
const flash = computed(() => page.props.flash);

const isSidebarOpen = ref(false);

const toggleSidebar = () => {
    isSidebarOpen.value = !isSidebarOpen.value;
};

const handleLogout = () => {
    router.post('/logout');
};

const currentUrl = computed(() => page.url);
const isActive = (path: string) => {
    if (path === '/dashboard') {
        return currentUrl.value === '/dashboard' || currentUrl.value === '/';
    }
    return currentUrl.value.startsWith(path);
};
</script>

<template>
    <div class="min-h-screen bg-light">
        <!-- Sidebar -->
        <aside class="noc-sidebar" :class="{ show: isSidebarOpen }">
            <div class="brand">
                <img src="/images/branding/logo-replay-net.png" alt="Replay" />
                <div>
                    <strong class="d-block text-white">REPLAY BOS</strong>
                    <small class="text-secondary">Business Operating System</small>
                </div>
            </div>

            <nav class="nav flex-column py-2">
                <a class="nav-link" :class="{ active: isActive('/dashboard') }" href="/dashboard">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>

                <a class="nav-link" :class="{ active: isActive('/portal-karyawan') }" href="/portal-karyawan">
                    <i class="bi bi-person-badge me-2"></i> Portal Karyawan
                </a>

                <a class="nav-link" :class="{ active: isActive('/reseller-portal') }" href="/reseller-portal">
                    <i class="bi bi-people me-2"></i> Pelanggan Saya
                </a>

                <a class="nav-link" :class="{ active: isActive('/tickets') }" href="/tickets">
                    <i class="bi bi-ticket-perforated me-2"></i> Tickets
                </a>

                <a class="nav-link" :class="{ active: isActive('/ticket-titles') }" href="/ticket-titles">
                    <i class="bi bi-card-heading me-2"></i> Master Judul Tiket
                </a>

                <a class="nav-link" :class="{ active: isActive('/customers') }" href="/customers">
                    <i class="bi bi-briefcase me-2"></i> Pelanggan
                </a>

                <a class="nav-link" :class="{ active: isActive('/quotations') }" href="/quotations">
                    <i class="bi bi-file-earmark-text me-2"></i> Surat Penawaran
                </a>

                <a class="nav-link" :class="{ active: isActive('/invoices') }" href="/invoices">
                    <i class="bi bi-receipt me-2"></i> Closing
                </a>

                <a class="nav-link" :class="{ active: isActive('/coverage-points') }" href="/coverage-points">
                    <i class="bi bi-geo-alt me-2"></i> Titik Coverage
                </a>

                <a class="nav-link" :class="{ active: isActive('/service-packages') }" href="/service-packages">
                    <i class="bi bi-box-seam me-2"></i> Paket Layanan
                </a>

                <a class="nav-link" :class="{ active: isActive('/teams') }" href="/teams">
                    <i class="bi bi-diagram-3 me-2"></i> Tim
                </a>

                <a class="nav-link" :class="{ active: isActive('/employees') }" href="/employees">
                    <i class="bi bi-people me-2"></i> Data Karyawan
                </a>

                <a class="nav-link" :class="{ active: isActive('/absensi/laporan') }" href="/absensi/laporan">
                    <i class="bi bi-calendar-check me-2"></i> Laporan Absensi
                </a>

                <a class="nav-link" :class="{ active: isActive('/cuti-izin/laporan') }" href="/cuti-izin/laporan">
                    <i class="bi bi-calendar-x me-2"></i> Pengajuan Cuti/Izin
                </a>

                <a class="nav-link" :class="{ active: isActive('/contract-templates') }" href="/contract-templates">
                    <i class="bi bi-file-earmark-text me-2"></i> Redaksi PKWT/PKWTT
                </a>

                <a class="nav-link" :class="{ active: isActive('/assets') }" href="/assets">
                    <i class="bi bi-box-seam me-2"></i> Aset / Inventaris
                </a>

                <a class="nav-link" :class="{ active: isActive('/asset-types') }" href="/asset-types">
                    <i class="bi bi-tags me-2"></i> Jenis Aset
                </a>

                <a class="nav-link" :class="{ active: isActive('/work-orders') }" href="/work-orders">
                    <i class="bi bi-clipboard-check me-2"></i> Work Order
                </a>

                <a class="nav-link" :class="{ active: isActive('/field-visit-reports') }" href="/field-visit-reports">
                    <i class="bi bi-geo-alt me-2"></i> Laporan Kunjungan
                </a>

                <a class="nav-link" :class="{ active: isActive('/users') }" href="/users">
                    <i class="bi bi-people me-2"></i> Users
                </a>

                <a class="nav-link" :class="{ active: isActive('/roles') }" href="/roles">
                    <i class="bi bi-shield-lock me-2"></i> Roles &amp; Permissions
                </a>

                <a class="nav-link" :class="{ active: isActive('/whatsapp-templates') }" href="/whatsapp-templates">
                    <i class="bi bi-whatsapp me-2"></i> Template WhatsApp
                </a>

                <a class="nav-link" :class="{ active: isActive('/audit') }" href="/audit">
                    <i class="bi bi-journal-text me-2"></i> Audit Trail
                </a>

                <a class="nav-link" :class="{ active: isActive('/settings') }" href="/settings">
                    <i class="bi bi-gear me-2"></i> Pengaturan
                </a>

                <a class="nav-link" :class="{ active: isActive('/api-tokens') }" href="/api-tokens">
                    <i class="bi bi-key me-2"></i> API Token
                </a>
            </nav>
        </aside>

        <!-- Main Content Area -->
        <div class="noc-content">
            <!-- Topbar -->
            <header class="noc-topbar d-flex align-items-center justify-content-between px-4 py-3">
                <div class="d-flex align-items-center">
                    <button @click="toggleSidebar" class="btn btn-sm btn-outline-secondary d-lg-none me-3" type="button">
                        <i class="bi bi-list"></i>
                    </button>
                    <h5 class="mb-0 fw-semibold">{{ title || 'Dashboard' }}</h5>
                </div>
                <div class="dropdown">
                    <button class="btn btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i> {{ user?.name || 'User' }}
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                        <li><span class="dropdown-item-text text-muted small">{{ user?.email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a href="/security/password" class="dropdown-item">
                                <i class="bi bi-key me-1"></i> Ganti Password
                            </a>
                        </li>
                        <li>
                            <a href="/security/two-factor" class="dropdown-item">
                                <i class="bi bi-shield-lock me-1"></i> Keamanan Akun (2FA)
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button @click="handleLogout" type="button" class="dropdown-item text-danger">
                                <i class="bi bi-box-arrow-right me-1"></i> Logout
                            </button>
                        </li>
                    </ul>
                </div>
            </header>

            <!-- Alerts -->
            <div class="px-4 pt-3" v-if="flash?.success || flash?.error">
                <div v-if="flash?.success" class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle me-2"></i> {{ flash.success }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div v-if="flash?.error" class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle me-2"></i> {{ flash.error }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>

            <!-- Page Slot -->
            <main class="p-4">
                <slot />
            </main>
        </div>
    </div>
</template>

<style scoped>
:root {
    --noc-navy: #0b1d3a;
    --noc-navy-dark: #071228;
    --noc-blue: #2f6fed;
    --noc-gray: #6b7785;
    --noc-border: #1c2f52;
}

.noc-sidebar {
    width: 250px;
    min-height: 100vh;
    background: #0b1d3a;
    color: #cfd8ea;
    position: fixed;
    top: 0;
    left: 0;
    bottom: 0;
    overflow-y: auto;
    z-index: 1040;
}

.noc-sidebar .brand {
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #1c2f52;
    display: flex;
    align-items: center;
    gap: .65rem;
}

.noc-sidebar .brand img {
    width: 36px;
    height: 36px;
    flex-shrink: 0;
}

.noc-sidebar .brand strong {
    color: #fff;
    font-size: 1.05rem;
}

.noc-sidebar .brand small {
    color: #6b7785;
}

.noc-sidebar .nav-link {
    color: #cfd8ea;
    padding: .6rem 1.25rem;
    border-left: 3px solid transparent;
    text-decoration: none;
    display: flex;
    align-items: center;
}

.noc-sidebar .nav-link:hover {
    background: rgba(255,255,255,.05);
    color: #fff;
}

.noc-sidebar .nav-link.active {
    background: rgba(47,111,237,.15);
    border-left-color: #2f6fed;
    color: #fff;
    font-weight: 600;
}

.noc-content {
    margin-left: 250px;
    min-height: 100vh;
    display: flex;
    flex-direction: column;
}

.noc-topbar {
    background: #fff;
    border-bottom: 1px solid #e3e7ee;
}

@media (max-width: 991.98px) {
    .noc-sidebar {
        transform: translateX(-100%);
        transition: transform .2s ease;
    }
    .noc-sidebar.show {
        transform: translateX(0);
    }
    .noc-content {
        margin-left: 0;
    }
}
</style>
