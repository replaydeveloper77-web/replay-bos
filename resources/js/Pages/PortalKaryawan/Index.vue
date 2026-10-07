<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

interface PortalEmployee {
    id: number;
    name: string;
    employee_number: string;
    position: string;
    department: string;
    employment_status: string;
    nik_ktp: string;
    gender: string;
    birth_place: string;
    birth_date?: string | null;
    email: string;
    phone: string;
    address: string;
    no_jmo: string;
    no_kpj: string;
    photo_url: string;
    join_date: string;
    resign_date: string;
    is_active: boolean;
}

interface TodayAttendance {
    id: number;
    is_checked_in: boolean;
    is_checked_out: boolean;
    check_in_at?: string | null;
    check_out_at?: string | null;
}

interface AttendanceHistoryItem {
    id: number;
    date: string;
    check_in_at?: string | null;
    check_out_at?: string | null;
}

interface LeaveQuota {
    quota: number;
    used: number;
    remaining: number;
}

interface LeaveRequestItem {
    id: number;
    type_label: string;
    start_date: string;
    end_date: string;
    status: string;
    status_label: string;
}

interface PortalUser {
    id: number;
    name: string;
    username: string;
    can_manage_employees: boolean;
}

const props = defineProps<{
    employee: PortalEmployee | null;
    user: PortalUser;
    todayDateFormatted: string;
    today: TodayAttendance | null;
    attendanceHistory: AttendanceHistoryItem[];
    leaveQuota: LeaveQuota | null;
    leaveRequests: LeaveRequestItem[];
}>();

// Form Check-in
const checkinForm = useForm({
    lat: '' as string | number,
    lng: '' as string | number,
    notes: '',
});

// Form Check-out
const checkoutForm = useForm({
    lat: '' as string | number,
    lng: '' as string | number,
    notes: '',
});

onMounted(() => {
    if ('geolocation' in navigator) {
        navigator.geolocation.getCurrentPosition(
            (pos) => {
                const lat = pos.coords.latitude;
                const lng = pos.coords.longitude;
                checkinForm.lat = lat;
                checkinForm.lng = lng;
                checkoutForm.lat = lat;
                checkoutForm.lng = lng;
            },
            () => {
                // Lokasi opsional
            },
            { enableHighAccuracy: true, timeout: 5000 }
        );
    }
});

const submitCheckin = () => {
    checkinForm.post('/absensi/check-in', {
        preserveScroll: true,
    });
};

const submitCheckout = () => {
    checkoutForm.post('/absensi/check-out', {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Portal Karyawan" />

    <AppLayout title="Portal Karyawan">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="mb-0 fw-bold">Portal Karyawan</h5>
                <small class="text-muted">Data Diri &amp; Layanan Mandiri Karyawan</small>
            </div>
            <div>
                <a
                    v-if="user.can_manage_employees"
                    href="/employees"
                    class="btn btn-outline-primary btn-sm"
                >
                    <i class="bi bi-people me-1"></i> Buka Data Karyawan (Semua)
                </a>
            </div>
        </div>

        <!-- Jika Akun Belum Terhubung Karyawan -->
        <div v-if="!employee" class="card border-0 shadow-sm p-4 text-center my-4 mx-auto" style="max-width: 600px; border-radius: 12px;">
            <div class="my-3">
                <i class="bi bi-person-x text-muted" style="font-size: 3.5rem;"></i>
            </div>
            <h5 class="fw-bold mb-2">Akun Belum Ditautkan ke Data Karyawan</h5>
            <p class="text-muted small mb-4">
                Akun login Anda (<strong>{{ user.name }}</strong> / <code>{{ user.username }}</code>) saat ini belum terhubung dengan master data karyawan mana pun di sistem.
            </p>
            <div v-if="user.can_manage_employees">
                <a href="/employees" class="btn btn-primary btn-sm px-3">
                    <i class="bi bi-link-45deg me-1"></i> Tautkan Akun di Menu Data Karyawan
                </a>
            </div>
            <div v-else class="alert alert-light border small text-muted mb-0">
                Silakan hubungi bagian <strong>HR</strong> atau <strong>Super Admin</strong> untuk menautkan akun Anda ke data karyawan.
            </div>
        </div>

        <!-- Jika Akun Sudah Terhubung Karyawan -->
        <div v-else>
            <!-- Banner Profil Karyawan -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
                <div class="p-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: #fff;">
                    <div class="d-flex flex-wrap align-items-center gap-4">
                        <div class="position-relative">
                            <img
                                :src="employee.photo_url"
                                :alt="employee.name"
                                class="rounded shadow-sm border border-3 border-white bg-white"
                                style="width: 105px; height: 140px; object-fit: cover;"
                            />
                            <span
                                v-if="employee.is_active"
                                class="position-absolute bottom-0 end-0 badge bg-success border border-white rounded-pill px-2 py-1 shadow-xs"
                                style="transform: translate(20%, 20%); font-size: 0.7rem;"
                            >
                                <i class="bi bi-check-circle-fill"></i> Aktif
                            </span>
                            <span
                                v-else
                                class="position-absolute bottom-0 end-0 badge bg-danger border border-white rounded-pill px-2 py-1 shadow-xs"
                                style="transform: translate(20%, 20%); font-size: 0.7rem;"
                            >
                                Nonaktif
                            </span>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <h4 class="mb-0 fw-bold">{{ employee.name }}</h4>
                                <span class="badge bg-white text-primary fw-semibold px-2 py-1">
                                    <code>{{ employee.employee_number }}</code>
                                </span>
                            </div>
                            <p class="mb-2 text-white-50 fs-6">
                                {{ employee.position }} • Departemen {{ employee.department }}
                            </p>
                            <div class="d-flex flex-wrap gap-3 small text-white-75">
                                <span v-if="employee.email !== '-'"><i class="bi bi-envelope me-1"></i> {{ employee.email }}</span>
                                <span v-if="employee.phone !== '-'"><i class="bi bi-whatsapp me-1"></i> {{ employee.phone }}</span>
                                <span v-if="employee.join_date !== '-'"><i class="bi bi-calendar-event me-1"></i> Masuk: {{ employee.join_date }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Detail Lengkap Data Diri -->
                <div class="card-body p-4 bg-white">
                    <div class="row g-4">
                        <!-- Informasi Kepegawaian & BPJS -->
                        <div class="col-lg-6 border-end">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <i class="bi bi-shield-check text-primary me-2"></i>Informasi Kepegawaian &amp; BPJS
                            </h6>
                            <div class="row g-3 small">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Nomor Induk Karyawan</span>
                                    <strong class="text-dark">{{ employee.employee_number }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Jabatan</span>
                                    <strong class="text-dark">{{ employee.position }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Departemen</span>
                                    <strong class="text-dark">{{ employee.department }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Status Kepegawaian</span>
                                    <strong class="text-dark">{{ employee.employment_status }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Nomor JMO (BPJS TK)</span>
                                    <strong class="text-primary fs-6">{{ employee.no_jmo }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Nomor KPJ (Jamsostek)</span>
                                    <strong class="text-primary fs-6">{{ employee.no_kpj }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Tahun / Tanggal Masuk</span>
                                    <strong class="text-dark">{{ employee.join_date }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Akhir Bekerja</span>
                                    <strong class="text-dark">{{ employee.resign_date }}</strong>
                                </div>
                            </div>
                        </div>

                        <!-- Data Identitas Pribadi -->
                        <div class="col-lg-6">
                            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                                <i class="bi bi-person-lines-fill text-primary me-2"></i>Data Identitas Pribadi
                            </h6>
                            <div class="row g-3 small">
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">NIK KTP</span>
                                    <strong class="text-dark">{{ employee.nik_ktp }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Jenis Kelamin</span>
                                    <strong class="text-dark">{{ employee.gender }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Tempat, Tanggal Lahir</span>
                                    <strong class="text-dark">
                                        {{ employee.birth_place }}<span v-if="employee.birth_date">, {{ employee.birth_date }}</span>
                                    </strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">Nomor Telepon / WA</span>
                                    <strong class="text-dark">{{ employee.phone }}</strong>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted d-block">Email</span>
                                    <strong class="text-dark">{{ employee.email }}</strong>
                                </div>
                                <div class="col-12">
                                    <span class="text-muted d-block">Alamat Tempat Tinggal</span>
                                    <div class="text-dark">{{ employee.address }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layanan Mandiri: Absensi & Cuti Pribadi -->
            <div class="row g-3">
                <!-- Absensi Hari Ini -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0"><i class="bi bi-clock text-success me-2"></i>Absensi Hari Ini</h6>
                            <span class="badge bg-light text-muted border">{{ todayDateFormatted }}</span>
                        </div>

                        <div class="bg-light p-3 rounded mb-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="small text-muted">Status Check-in:</span>
                                <span v-if="today && today.is_checked_in" class="badge bg-success">
                                    <i class="bi bi-check2"></i> {{ today.check_in_at }} WIB
                                </span>
                                <span v-else class="badge bg-secondary">Belum Check-in</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small text-muted">Status Check-out:</span>
                                <span v-if="today && today.is_checked_out" class="badge bg-danger">
                                    <i class="bi bi-box-arrow-right"></i> {{ today.check_out_at }} WIB
                                </span>
                                <span v-else class="badge bg-secondary">Belum Check-out</span>
                            </div>
                        </div>

                        <!-- Form Check-in -->
                        <div v-if="!today || !today.is_checked_in">
                            <form @submit.prevent="submitCheckin">
                                <div class="mb-2">
                                    <textarea
                                        v-model="checkinForm.notes"
                                        class="form-control form-control-sm"
                                        rows="2"
                                        placeholder="Catatan kerja (opsional, misal WFH / Dinas Lapangan)"
                                    ></textarea>
                                </div>
                                <button
                                    type="submit"
                                    class="btn btn-success w-100"
                                    :disabled="checkinForm.processing"
                                >
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    {{ checkinForm.processing ? 'Menyimpan...' : 'Check-in Sekarang' }}
                                </button>
                            </form>
                        </div>

                        <!-- Form Check-out -->
                        <div v-else-if="!today.is_checked_out">
                            <form @submit.prevent="submitCheckout">
                                <div class="mb-2">
                                    <textarea
                                        v-model="checkoutForm.notes"
                                        class="form-control form-control-sm"
                                        rows="2"
                                        placeholder="Catatan penutup (opsional)"
                                    ></textarea>
                                </div>
                                <button
                                    type="submit"
                                    class="btn btn-danger w-100"
                                    :disabled="checkoutForm.processing"
                                >
                                    <i class="bi bi-box-arrow-right me-1"></i>
                                    {{ checkoutForm.processing ? 'Menyimpan...' : 'Check-out Sekarang' }}
                                </button>
                            </form>
                        </div>

                        <!-- Sudah Lengkap -->
                        <div v-else class="alert alert-success small mb-0 text-center">
                            <i class="bi bi-check-circle-fill me-1"></i> Absensi hari ini sudah lengkap. Terima kasih atas kerja keras Anda!
                        </div>

                        <!-- Riwayat Absensi -->
                        <div v-if="attendanceHistory.length > 0" class="mt-4 pt-3 border-top">
                            <h6 class="small fw-semibold text-muted mb-2">Riwayat Absensi 7 Hari Terakhir:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless small mb-0">
                                    <tbody>
                                        <tr v-for="att in attendanceHistory" :key="att.id">
                                            <td class="text-muted">{{ att.date }}</td>
                                            <td>
                                                <span class="text-success">
                                                    <i class="bi bi-arrow-down-right"></i> {{ att.check_in_at || '-' }}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="text-danger">
                                                    <i class="bi bi-arrow-up-right"></i> {{ att.check_out_at || '-' }}
                                                </span>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kuota & Cuti Pribadi -->
                <div class="col-lg-6">
                    <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 12px;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0"><i class="bi bi-calendar-check text-primary me-2"></i>Cuti &amp; Izin</h6>
                            <a href="/cuti-izin" class="btn btn-primary btn-sm py-1 px-3">
                                <i class="bi bi-plus-lg me-1"></i> Ajukan Cuti/Izin
                            </a>
                        </div>

                        <div v-if="employee.employment_status.toUpperCase() === 'TETAP' && leaveQuota" class="bg-light p-3 rounded mb-3">
                            <div class="small fw-semibold text-muted mb-2">Kuota Cuti Tahunan:</div>
                            <div class="d-flex justify-content-between align-items-center text-center">
                                <div>
                                    <div class="fs-4 fw-bold text-dark">{{ leaveQuota.quota }}</div>
                                    <small class="text-muted">Total Hak</small>
                                </div>
                                <div class="border-start ps-3">
                                    <div class="fs-4 fw-bold text-danger">{{ leaveQuota.used }}</div>
                                    <small class="text-muted">Terpakai</small>
                                </div>
                                <div class="border-start ps-3">
                                    <div class="fs-4 fw-bold text-success">{{ leaveQuota.remaining }}</div>
                                    <small class="text-muted">Sisa Kuota</small>
                                </div>
                            </div>
                        </div>

                        <h6 class="small fw-semibold text-muted mb-2">Pengajuan Cuti/Izin Terakhir:</h6>
                        <div v-if="leaveRequests.length === 0" class="text-muted small py-3 text-center mb-0">
                            Belum ada riwayat pengajuan cuti/izin.
                        </div>
                        <div v-else class="table-responsive">
                            <table class="table table-sm align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="lr in leaveRequests" :key="lr.id">
                                        <td><strong>{{ lr.type_label }}</strong></td>
                                        <td class="text-nowrap">{{ lr.start_date }} - {{ lr.end_date }}</td>
                                        <td>
                                            <span
                                                class="badge"
                                                :class="{
                                                    'bg-success': lr.status === 'APPROVED',
                                                    'bg-danger': lr.status === 'REJECTED',
                                                    'bg-secondary': lr.status === 'CANCELLED',
                                                    'bg-warning text-dark': lr.status === 'PENDING'
                                                }"
                                            >
                                                {{ lr.status_label }}
                                            </span>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
