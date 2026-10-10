<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
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
    check_in_lat?: number | string | null;
    check_in_lng?: number | string | null;
    check_in_address?: string | null;
    check_in_photo_url?: string | null;
    check_out_at?: string | null;
    check_out_lat?: number | string | null;
    check_out_lng?: number | string | null;
    check_out_address?: string | null;
    check_out_photo_url?: string | null;
    notes?: string | null;
}

interface AttendanceHistoryItem {
    id: number;
    date: string;
    check_in_at?: string | null;
    check_in_lat?: number | string | null;
    check_in_lng?: number | string | null;
    check_in_address?: string | null;
    check_in_photo_url?: string | null;
    check_out_at?: string | null;
    check_out_lat?: number | string | null;
    check_out_lng?: number | string | null;
    check_out_address?: string | null;
    check_out_photo_url?: string | null;
    notes?: string | null;
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
    address: '',
    photo: '',
    notes: '',
});

// Form Check-out
const checkoutForm = useForm({
    lat: '' as string | number,
    lng: '' as string | number,
    address: '',
    photo: '',
    notes: '',
});

// State Kamera & Modal Absensi Live
const showCameraModal = ref(false);
const cameraMode = ref<'CHECK-IN' | 'CHECK-OUT'>('CHECK-IN');
const videoRef = ref<HTMLVideoElement | null>(null);
const mediaStream = ref<MediaStream | null>(null);
const cameraActive = ref(false);
const cameraError = ref('');
const capturedPhoto = ref<string | null>(null);
const isProcessing = ref(false);
const attendanceNotes = ref('');

const attendanceLocation = ref<{
    lat: number | string | null;
    lng: number | string | null;
    address: string;
    loading: boolean;
}>({
    lat: null,
    lng: null,
    address: '',
    loading: false,
});

// Helper aman untuk format koordinat GPS (mendukung number, string, null)
const formatCoordinate = (coord: number | string | null | undefined, precision = 6): string => {
    if (coord === null || coord === undefined || coord === '') return '-';
    const num = Number(coord);
    return isNaN(num) ? '-' : num.toFixed(precision);
};

// Lightbox Modal Foto
const activePhotoModal = ref<{
    isOpen: boolean;
    url: string;
    title: string;
    time: string;
    address: string;
    lat: number | string | null;
    lng: number | string | null;
}>({
    isOpen: false,
    url: '',
    title: '',
    time: '',
    address: '',
    lat: null,
    lng: null,
});

// Mengambil Geolocation & Reverse Geocoding
const fetchLocation = async () => {
    if (!('geolocation' in navigator)) {
        attendanceLocation.value.address = 'Geolocation tidak didukung pada browser ini';
        return;
    }

    attendanceLocation.value.loading = true;
    navigator.geolocation.getCurrentPosition(
        async (pos) => {
            const lat = pos.coords.latitude;
            const lng = pos.coords.longitude;
            attendanceLocation.value.lat = lat;
            attendanceLocation.value.lng = lng;

            try {
                const controller = new AbortController();
                const timeoutId = setTimeout(() => controller.abort(), 4000);
                const res = await fetch(
                    `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1`,
                    {
                        signal: controller.signal,
                        headers: { Accept: 'application/json' },
                    }
                );
                clearTimeout(timeoutId);
                if (res.ok) {
                    const data = await res.json();
                    if (data.display_name) {
                        attendanceLocation.value.address = data.display_name;
                    } else {
                        attendanceLocation.value.address = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                    }
                } else {
                    attendanceLocation.value.address = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                }
            } catch {
                attendanceLocation.value.address = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            } finally {
                attendanceLocation.value.loading = false;
            }
        },
        () => {
            attendanceLocation.value.loading = false;
            attendanceLocation.value.address = 'Izin lokasi tidak diberikan / tidak dapat diambil';
        },
        { enableHighAccuracy: true, timeout: 8000 }
    );
};

// Kamera Akses
const startCamera = async () => {
    cameraError.value = '';
    cameraActive.value = false;
    try {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            cameraError.value = 'Perangkat atau peramban tidak mendukung akses kamera langsung.';
            return;
        }

        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                facingMode: 'user',
                width: { ideal: 1280 },
                height: { ideal: 720 },
            },
            audio: false,
        });

        mediaStream.value = stream;
        cameraActive.value = true;

        if (videoRef.value) {
            videoRef.value.srcObject = stream;
            videoRef.value.play().catch(() => {});
        }
    } catch (err: any) {
        cameraActive.value = false;
        cameraError.value = 'Tidak dapat mengakses kamera. Pastikan izin kamera telah diberikan pada peramban perangkat Anda.';
    }
};

const stopCamera = () => {
    if (mediaStream.value) {
        mediaStream.value.getTracks().forEach((track) => track.stop());
        mediaStream.value = null;
    }
    cameraActive.value = false;
};

// Logo Replay Loader
const loadLogo = (): Promise<HTMLImageElement | null> => {
    return new Promise((resolve) => {
        const img = new Image();
        img.crossOrigin = 'anonymous';
        img.onload = () => resolve(img);
        img.onerror = () => resolve(null);
        img.src = '/images/branding/logo-replay-net.png';
    });
};

// Generator Watermark Canvas Resmi Replay
const generateWatermarkedPhoto = async (
    source: HTMLVideoElement,
    isMirror = true
): Promise<string> => {
    const canvas = document.createElement('canvas');
    const width = 960;
    let height = 720;
    if (source.videoWidth && source.videoHeight) {
        height = Math.round((source.videoHeight / source.videoWidth) * width) || 720;
    }
    canvas.width = width;
    canvas.height = height;

    const ctx = canvas.getContext('2d');
    if (!ctx) throw new Error('Canvas context not available');

    // 1. Gambar Frame Foto dari Kamera
    if (isMirror) {
        ctx.save();
        ctx.translate(width, 0);
        ctx.scale(-1, 1);
        ctx.drawImage(source, 0, 0, width, height);
        ctx.restore();
    } else {
        ctx.drawImage(source, 0, 0, width, height);
    }

    // 2. Format Timestamp Aktual Pengambilan Foto (Zona Waktu WIB)
    const snapTime = new Date();
    const wibFormatter = new Intl.DateTimeFormat('id-ID', {
        timeZone: 'Asia/Jakarta',
        weekday: 'long',
        day: 'numeric',
        month: 'long',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit',
        hour12: false,
    });
    const parts = wibFormatter.formatToParts(snapTime);
    const partMap: Record<string, string> = {};
    for (const p of parts) partMap[p.type] = p.value;
    const dayName = partMap.weekday || 'Minggu';
    const dayDate = partMap.day || String(snapTime.getDate()).padStart(2, '0');
    const monthName = partMap.month || 'Januari';
    const yearNum = partMap.year || String(snapTime.getFullYear());
    const hours = partMap.hour || String(snapTime.getHours()).padStart(2, '0');
    const mins = partMap.minute || String(snapTime.getMinutes()).padStart(2, '0');
    const secs = partMap.second || String(snapTime.getSeconds()).padStart(2, '0');
    const timeFormatted = `${dayName}, ${dayDate} ${monthName} ${yearNum} · ${hours}:${mins}:${secs} WIB`;

    // 3. Informasi Alamat dan Koordinat GPS Aktual
    let addrText = attendanceLocation.value.address ? attendanceLocation.value.address.trim() : '';
    if (!addrText) {
        addrText = attendanceLocation.value.lat ? 'Alamat belum ditemukan' : 'Lokasi belum terdeteksi';
    }

    let gpsText = '';
    if (
        attendanceLocation.value.lat !== null &&
        attendanceLocation.value.lat !== undefined &&
        attendanceLocation.value.lat !== '' &&
        attendanceLocation.value.lng !== null &&
        attendanceLocation.value.lng !== undefined &&
        attendanceLocation.value.lng !== ''
    ) {
        gpsText = `GPS: ${formatCoordinate(attendanceLocation.value.lat, 6)}, ${formatCoordinate(attendanceLocation.value.lng, 6)}`;
    } else {
        gpsText = 'GPS: Belum tersedia';
    }

    // 4. Overlay Gelap Semi-Transparan Bagian Bawah Foto
    const overlayHeight = 116;
    const bottomGrad = ctx.createLinearGradient(0, height - overlayHeight - 20, 0, height);
    bottomGrad.addColorStop(0, 'rgba(0, 0, 0, 0)');
    bottomGrad.addColorStop(0.2, 'rgba(15, 23, 42, 0.78)');
    bottomGrad.addColorStop(1, 'rgba(15, 23, 42, 0.94)');
    ctx.fillStyle = bottomGrad;
    ctx.fillRect(0, height - overlayHeight - 20, width, overlayHeight + 20);

    // 5. Logo Resmi Replay di sebelah kiri
    const logoImg = await loadLogo();
    const logoSize = 64;
    const logoX = 24;
    const logoY = height - 94;
    if (logoImg) {
        ctx.drawImage(logoImg, logoX, logoY, logoSize, logoSize);
    }

    // 6. Susunan Watermark Resmi di sebelah kanan logo
    const textX = logoX + logoSize + 16;
    const maxTextWidth = width - textX - 24;

    const truncateText = (text: string, maxWidth: number): string => {
        if (ctx.measureText(text).width <= maxWidth) return text;
        let truncated = text;
        while (truncated.length > 0 && ctx.measureText(truncated + '...').width > maxWidth) {
            truncated = truncated.slice(0, -1);
        }
        return truncated + '...';
    };

    // Baris 1: Teks Perusahaan PT. Replay Inti Media
    ctx.fillStyle = '#ffffff';
    ctx.font = 'bold 16px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    ctx.fillText('PT. Replay Inti Media', textX, height - 74);

    // Baris 2: Hari, Tanggal, Bulan, Tahun · Jam:Menit:Detik WIB
    ctx.fillStyle = '#f1f5f9';
    ctx.font = '500 13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    ctx.fillText(timeFormatted, textX, height - 54);

    // Baris 3: Alamat Lokasi
    ctx.fillStyle = '#e2e8f0';
    ctx.font = '12px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    ctx.fillText(truncateText(addrText, maxTextWidth), textX, height - 36);

    // Baris 4: Koordinat GPS
    ctx.fillStyle = '#cbd5e1';
    ctx.font = '12px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
    ctx.fillText(gpsText, textX, height - 18);

    return canvas.toDataURL('image/jpeg', 0.90);
};

// Snapshot & Handling
const takeSnapshot = async () => {
    if (!videoRef.value) return;
    isProcessing.value = true;
    try {
        const dataUrl = await generateWatermarkedPhoto(videoRef.value, true);
        capturedPhoto.value = dataUrl;
        stopCamera();
    } catch (e: any) {
        alert('Gagal mengambil foto: ' + e.message);
    } finally {
        isProcessing.value = false;
    }
};

const retakePhoto = () => {
    capturedPhoto.value = null;
    startCamera();
};

const openAttendanceDialog = (mode: 'CHECK-IN' | 'CHECK-OUT') => {
    cameraMode.value = mode;
    capturedPhoto.value = null;
    attendanceNotes.value = '';
    showCameraModal.value = true;
    fetchLocation();
    setTimeout(() => {
        startCamera();
    }, 150);
};

const closeAttendanceDialog = () => {
    stopCamera();
    showCameraModal.value = false;
    capturedPhoto.value = null;
    attendanceNotes.value = '';
};

const confirmAndSubmitAttendance = () => {
    if (!capturedPhoto.value) return;

    if (cameraMode.value === 'CHECK-IN') {
        if (checkinForm.processing) return;
        checkinForm.lat = attendanceLocation.value.lat || '';
        checkinForm.lng = attendanceLocation.value.lng || '';
        checkinForm.address = attendanceLocation.value.address || '';
        checkinForm.photo = capturedPhoto.value;
        checkinForm.notes = attendanceNotes.value;
        checkinForm.post('/absensi/check-in', {
            preserveScroll: true,
            onSuccess: () => {
                closeAttendanceDialog();
            },
        });
    } else {
        if (checkoutForm.processing) return;
        checkoutForm.lat = attendanceLocation.value.lat || '';
        checkoutForm.lng = attendanceLocation.value.lng || '';
        checkoutForm.address = attendanceLocation.value.address || '';
        checkoutForm.photo = capturedPhoto.value;
        checkoutForm.notes = attendanceNotes.value;
        checkoutForm.post('/absensi/check-out', {
            preserveScroll: true,
            onSuccess: () => {
                closeAttendanceDialog();
            },
        });
    }
};

const openPhotoModal = (
    url: string,
    title: string,
    time: string,
    address?: string | null,
    lat?: number | string | null,
    lng?: number | string | null
) => {
    activePhotoModal.value = {
        isOpen: true,
        url,
        title,
        time,
        address: address || '-',
        lat: lat ?? null,
        lng: lng ?? null,
    };
};

onMounted(() => {
    fetchLocation();
});

onBeforeUnmount(() => {
    stopCamera();
});
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
                                    <span class="text-muted d-block">No. BPJS Ketenagakerjaan</span>
                                    <strong class="text-primary fs-6">{{ employee.no_jmo }}</strong>
                                </div>
                                <div class="col-sm-6">
                                    <span class="text-muted d-block">No. BPJS Kesehatan</span>
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

                        <!-- Ringkasan Status & Detail Hari Ini -->
                        <div class="mb-3">
                            <!-- Detail Check-in -->
                            <div class="p-3 rounded-3 mb-2 border" :class="today && today.is_checked_in ? 'border-success-subtle bg-success-subtle bg-opacity-10' : 'bg-light'">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-in-right fs-5" :class="today && today.is_checked_in ? 'text-success' : 'text-muted'"></i>
                                        <div>
                                            <span class="small fw-semibold d-block text-secondary">Check-in Masuk</span>
                                            <strong v-if="today && today.is_checked_in" class="text-success fs-6">
                                                {{ today.check_in_at }} WIB
                                            </strong>
                                            <span v-else class="badge bg-secondary-subtle text-secondary border">Belum Check-in</span>
                                        </div>
                                    </div>
                                    <!-- Thumbnail Foto Check-in Jika Ada -->
                                    <div v-if="today && today.check_in_photo_url" class="position-relative">
                                        <button
                                            type="button"
                                            class="btn p-0 border-0"
                                            title="Klik untuk memperbesar foto absensi"
                                            @click="openPhotoModal(today.check_in_photo_url, 'Foto Absensi Check-in', today.check_in_at + ' WIB', today.check_in_address, today.check_in_lat, today.check_in_lng)"
                                        >
                                            <img
                                                :src="today.check_in_photo_url"
                                                alt="Foto Check-in"
                                                class="rounded-3 shadow-xs border border-success"
                                                style="width: 58px; height: 58px; object-fit: cover;"
                                            />
                                            <span class="position-absolute bottom-0 end-0 badge bg-dark bg-opacity-75 p-1" style="font-size: 0.65rem;">
                                                <i class="bi bi-zoom-in text-white"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <!-- Alamat & Koordinat Check-in -->
                                <div v-if="today && today.is_checked_in && (today.check_in_address || today.check_in_lat)" class="mt-2 pt-2 border-top border-success-subtle small text-muted">
                                    <div class="d-flex align-items-start gap-1 mb-1">
                                        <i class="bi bi-geo-alt-fill text-danger flex-shrink-0 mt-1"></i>
                                        <span class="text-dark">{{ today.check_in_address || 'Alamat tersimpan' }}</span>
                                    </div>
                                    <div v-if="today.check_in_lat && today.check_in_lng" class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-secondary border">
                                            GPS: {{ formatCoordinate(today.check_in_lat, 5) }}, {{ formatCoordinate(today.check_in_lng, 5) }}
                                        </span>
                                        <a
                                            :href="`https://www.google.com/maps?q=${today.check_in_lat},${today.check_in_lng}`"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-primary text-decoration-none small fw-semibold"
                                        >
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Buka Peta
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <!-- Detail Check-out -->
                            <div class="p-3 rounded-3 mb-2 border" :class="today && today.is_checked_out ? 'border-danger-subtle bg-danger-subtle bg-opacity-10' : 'bg-light'">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-box-arrow-right fs-5" :class="today && today.is_checked_out ? 'text-danger' : 'text-muted'"></i>
                                        <div>
                                            <span class="small fw-semibold d-block text-secondary">Check-out Pulang</span>
                                            <strong v-if="today && today.is_checked_out" class="text-danger fs-6">
                                                {{ today.check_out_at }} WIB
                                            </strong>
                                            <span v-else class="badge bg-secondary-subtle text-secondary border">Belum Check-out</span>
                                        </div>
                                    </div>
                                    <!-- Thumbnail Foto Check-out Jika Ada -->
                                    <div v-if="today && today.check_out_photo_url" class="position-relative">
                                        <button
                                            type="button"
                                            class="btn p-0 border-0"
                                            title="Klik untuk memperbesar foto absensi"
                                            @click="openPhotoModal(today.check_out_photo_url, 'Foto Absensi Check-out', today.check_out_at + ' WIB', today.check_out_address, today.check_out_lat, today.check_out_lng)"
                                        >
                                            <img
                                                :src="today.check_out_photo_url"
                                                alt="Foto Check-out"
                                                class="rounded-3 shadow-xs border border-danger"
                                                style="width: 58px; height: 58px; object-fit: cover;"
                                            />
                                            <span class="position-absolute bottom-0 end-0 badge bg-dark bg-opacity-75 p-1" style="font-size: 0.65rem;">
                                                <i class="bi bi-zoom-in text-white"></i>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                                <!-- Alamat & Koordinat Check-out -->
                                <div v-if="today && today.is_checked_out && (today.check_out_address || today.check_out_lat)" class="mt-2 pt-2 border-top border-danger-subtle small text-muted">
                                    <div class="d-flex align-items-start gap-1 mb-1">
                                        <i class="bi bi-geo-alt-fill text-danger flex-shrink-0 mt-1"></i>
                                        <span class="text-dark">{{ today.check_out_address || 'Alamat tersimpan' }}</span>
                                    </div>
                                    <div v-if="today.check_out_lat && today.check_out_lng" class="d-flex align-items-center gap-2">
                                        <span class="badge bg-light text-secondary border">
                                            GPS: {{ formatCoordinate(today.check_out_lat, 5) }}, {{ formatCoordinate(today.check_out_lng, 5) }}
                                        </span>
                                        <a
                                            :href="`https://www.google.com/maps?q=${today.check_out_lat},${today.check_out_lng}`"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                            class="text-primary text-decoration-none small fw-semibold"
                                        >
                                            <i class="bi bi-box-arrow-up-right me-1"></i>Buka Peta
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tombol Aksi Check-in / Check-out -->
                        <div v-if="!today || !today.is_checked_in">
                            <button
                                type="button"
                                class="btn btn-success w-100 py-2 fw-semibold shadow-sm"
                                :disabled="checkinForm.processing"
                                @click="openAttendanceDialog('CHECK-IN')"
                            >
                                <i class="bi bi-camera-fill me-1"></i>
                                {{ checkinForm.processing ? 'Menyimpan...' : 'Ambil Foto & Check-in Sekarang' }}
                            </button>
                            <small class="text-muted d-block text-center mt-2" style="font-size: 0.8rem;">
                                <i class="bi bi-shield-lock-fill text-success me-1"></i>Foto otomatis dilengkapi watermark logo Replay, koordinat, dan waktu real-time
                            </small>
                        </div>

                        <div v-else-if="!today.is_checked_out">
                            <button
                                type="button"
                                class="btn btn-danger w-100 py-2 fw-semibold shadow-sm"
                                :disabled="checkoutForm.processing"
                                @click="openAttendanceDialog('CHECK-OUT')"
                            >
                                <i class="bi bi-camera-fill me-1"></i>
                                {{ checkoutForm.processing ? 'Menyimpan...' : 'Ambil Foto & Check-out Sekarang' }}
                            </button>
                            <small class="text-muted d-block text-center mt-2" style="font-size: 0.8rem;">
                                <i class="bi bi-shield-lock-fill text-danger me-1"></i>Foto kepulangan otomatis dibubuhkan watermark resmi Replay & lokasi
                            </small>
                        </div>

                        <!-- Sudah Lengkap -->
                        <div v-else class="alert alert-success small mb-0 text-center py-3 border-0 bg-success-subtle text-success-emphasis rounded-3">
                            <i class="bi bi-check-circle-fill fs-5 d-block mb-1"></i>
                            <strong>Absensi Hari Ini Lengkap!</strong>
                            <div class="small">Terima kasih atas dedikasi dan kerja keras Anda hari ini.</div>
                        </div>

                        <!-- Riwayat Absensi -->
                        <div v-if="attendanceHistory.length > 0" class="mt-4 pt-3 border-top">
                            <h6 class="small fw-semibold text-muted mb-2">Riwayat Absensi 7 Hari Terakhir:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless small mb-0 align-middle">
                                    <tbody>
                                        <tr v-for="att in attendanceHistory" :key="att.id" class="border-bottom-subtle">
                                            <td class="text-muted py-2">{{ att.date }}</td>
                                            <td class="py-2">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="text-success">
                                                        <i class="bi bi-arrow-down-right"></i> {{ att.check_in_at || '-' }}
                                                    </span>
                                                    <button
                                                        v-if="att.check_in_photo_url"
                                                        type="button"
                                                        class="btn btn-xs btn-outline-success p-0 px-1 border-0"
                                                        title="Lihat foto check-in"
                                                        @click="openPhotoModal(att.check_in_photo_url, `Absensi Check-in (${att.date})`, att.check_in_at + ' WIB', att.check_in_address, att.check_in_lat, att.check_in_lng)"
                                                    >
                                                        <i class="bi bi-camera"></i>
                                                    </button>
                                                </div>
                                            </td>
                                            <td class="py-2">
                                                <div class="d-flex align-items-center gap-1">
                                                    <span class="text-danger">
                                                        <i class="bi bi-arrow-up-right"></i> {{ att.check_out_at || '-' }}
                                                    </span>
                                                    <button
                                                        v-if="att.check_out_photo_url"
                                                        type="button"
                                                        class="btn btn-xs btn-outline-danger p-0 px-1 border-0"
                                                        title="Lihat foto check-out"
                                                        @click="openPhotoModal(att.check_out_photo_url, `Absensi Check-out (${att.date})`, att.check_out_at + ' WIB', att.check_out_address, att.check_out_lat, att.check_out_lng)"
                                                    >
                                                        <i class="bi bi-camera"></i>
                                                    </button>
                                                </div>
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

        <!-- =============================================================== -->
        <!-- MODAL KAMERA & ABSENSI RESMI REPLAY -->
        <!-- =============================================================== -->
        <div
            v-if="showCameraModal"
            class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3"
            style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1060;"
            @click.self="closeAttendanceDialog"
        >
            <div
                class="card border-0 shadow-lg bg-white overflow-hidden w-100"
                style="max-width: 520px; border-radius: 14px; max-height: 94vh; display: flex; flex-direction: column;"
            >
                <!-- 1. Header Modal: Minimalis, Bersih, Profesional -->
                <div class="px-4 py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <img
                            src="/images/branding/logo-replay-net.png"
                            alt="Replay"
                            style="width: 28px; height: 28px; object-fit: contain;"
                        />
                        <h6 class="mb-0 fw-semibold text-dark fs-6">
                            Absensi {{ cameraMode === 'CHECK-IN' ? 'Check-in' : 'Check-out' }}
                        </h6>
                    </div>
                    <button
                        type="button"
                        class="btn-close"
                        aria-label="Tutup"
                        @click="closeAttendanceDialog"
                    ></button>
                </div>

                <!-- 2. Body Modal -->
                <div class="p-3 overflow-y-auto" style="flex: 1 1 auto;">
                    <!-- Informasi Lokasi Pengguna (Alamat & GPS) -->
                    <div class="p-3 mb-3 rounded-3 bg-light border d-flex align-items-start justify-content-between gap-2">
                        <div class="d-flex align-items-start gap-2.5 flex-grow-1 min-w-0">
                            <i class="bi bi-geo-alt text-success fs-5 flex-shrink-0 mt-0.5"></i>
                            <div class="min-w-0 flex-grow-1">
                                <div class="fw-semibold text-dark text-truncate-2" style="font-size: 0.825rem; line-height: 1.35;">
                                    <span v-if="attendanceLocation.loading" class="text-muted d-inline-flex align-items-center gap-1.5">
                                        <span class="spinner-border spinner-border-sm" role="status" style="width: 12px; height: 12px;"></span>
                                        Mendeteksi lokasi...
                                    </span>
                                    <span v-else>
                                        {{ attendanceLocation.address || 'Lokasi belum terdeteksi' }}
                                    </span>
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                    <span v-if="attendanceLocation.lat && attendanceLocation.lng">
                                        GPS: {{ formatCoordinate(attendanceLocation.lat, 6) }}, {{ formatCoordinate(attendanceLocation.lng, 6) }}
                                    </span>
                                    <span v-else-if="!attendanceLocation.loading">
                                        GPS: Belum tersedia
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button
                            type="button"
                            class="btn btn-outline-secondary btn-sm py-1 px-2 flex-shrink-0 ms-2"
                            style="font-size: 0.75rem;"
                            :disabled="attendanceLocation.loading"
                            @click="fetchLocation"
                            title="Segarkan Lokasi"
                        >
                            <i class="bi bi-arrow-clockwise" :class="{ 'spin-icon': attendanceLocation.loading }"></i>
                        </button>
                    </div>

                    <!-- Area Kamera Langsung & Pratinjau Foto -->
                    <div
                        class="position-relative rounded-3 overflow-hidden text-center mb-3"
                        style="aspect-ratio: 4/3; max-height: 380px; background-color: #0f172a;"
                    >
                        <!-- Live Camera Stream -->
                        <div v-show="!capturedPhoto" class="w-100 h-100 position-relative d-flex align-items-center justify-content-center">
                            <video
                                ref="videoRef"
                                autoplay
                                playsinline
                                muted
                                class="w-100 h-100"
                                style="object-fit: cover; transform: scaleX(-1);"
                            ></video>

                            <!-- Indikator Kamera Aktif (LIVE) -->
                            <div
                                v-if="cameraActive"
                                class="position-absolute top-0 start-0 m-3 px-2 py-1 rounded-2 d-flex align-items-center gap-1.5 shadow-sm"
                                style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px);"
                            >
                                <span class="bg-danger rounded-circle d-inline-block live-dot" style="width: 7px; height: 7px;"></span>
                                <span class="text-white fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">LIVE</span>
                            </div>

                            <!-- Penanganan Izin Kamera / Error -->
                            <div
                                v-if="cameraError"
                                class="position-absolute w-100 h-100 p-4 d-flex flex-column align-items-center justify-content-center text-white"
                                style="top: 0; left: 0; background: #0f172a;"
                            >
                                <i class="bi bi-camera-video-off fs-1 text-secondary mb-2"></i>
                                <p class="small text-white-50 mb-3 text-center" style="max-width: 320px;">
                                    {{ cameraError }}
                                </p>
                                <button type="button" class="btn btn-outline-light btn-sm px-3" @click="startCamera">
                                    <i class="bi bi-arrow-clockwise me-1"></i> Coba Akses Kamera Lagi
                                </button>
                            </div>
                        </div>

                        <!-- Pratinjau Foto Final Hasil Jepretan Kamera (Watermark Permanen) -->
                        <div v-if="capturedPhoto" class="w-100 h-100 position-relative bg-black">
                            <img
                                :src="capturedPhoto"
                                alt="Hasil Foto Absensi"
                                class="w-100 h-100"
                                style="object-fit: contain;"
                            />
                        </div>

                        <!-- Loading Saat Pembuatan Watermark -->
                        <div
                            v-if="isProcessing"
                            class="position-absolute w-100 h-100 d-flex flex-column align-items-center justify-content-center text-white"
                            style="top: 0; left: 0; background: rgba(15, 23, 42, 0.75);"
                        >
                            <div class="spinner-border text-light mb-2" role="status" style="width: 2rem; height: 2rem;"></div>
                            <span class="small">Memproses Foto...</span>
                        </div>
                    </div>

                    <!-- Tombol Pengambilan Foto / Ambil Ulang -->
                    <div class="mb-3">
                        <button
                            v-if="!capturedPhoto"
                            type="button"
                            class="btn btn-success w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm"
                            :disabled="isProcessing || !cameraActive"
                            @click="takeSnapshot"
                        >
                            <i class="bi bi-camera-fill"></i>
                            <span>Ambil Foto</span>
                        </button>

                        <button
                            v-if="capturedPhoto"
                            type="button"
                            class="btn btn-outline-secondary w-100 py-2 fw-semibold d-flex align-items-center justify-content-center gap-2"
                            @click="retakePhoto"
                        >
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Ambil Ulang Foto</span>
                        </button>
                    </div>

                    <!-- Kolom Catatan Kerja Opsional -->
                    <div>
                        <label class="form-label small fw-semibold text-secondary mb-1">
                            Catatan Kerja (Opsional)
                        </label>
                        <textarea
                            v-model="attendanceNotes"
                            class="form-control form-control-sm"
                            rows="2"
                            placeholder="Tuliskan catatan kerja jika ada..."
                        ></textarea>
                    </div>
                </div>

                <!-- 3. Footer Modal -->
                <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <button
                        type="button"
                        class="btn btn-outline-secondary btn-sm px-3"
                        :disabled="checkinForm.processing || checkoutForm.processing"
                        @click="closeAttendanceDialog"
                    >
                        Batal
                    </button>
                    <button
                        type="button"
                        class="btn btn-sm px-4 py-1.5 fw-semibold text-white shadow-sm"
                        :class="cameraMode === 'CHECK-IN' ? 'btn-success' : 'btn-danger'"
                        :disabled="!capturedPhoto || checkinForm.processing || checkoutForm.processing"
                        @click="confirmAndSubmitAttendance"
                    >
                        <span v-if="checkinForm.processing || checkoutForm.processing" class="d-flex align-items-center gap-1.5">
                            <span class="spinner-border spinner-border-sm" role="status"></span>
                            Menyimpan...
                        </span>
                        <span v-else class="d-flex align-items-center gap-1.5">
                            <i class="bi bi-check-lg"></i>
                            Simpan &amp; {{ cameraMode === 'CHECK-IN' ? 'Check-in' : 'Check-out' }}
                        </span>
                    </button>
                </div>
            </div>
        </div>

        <!-- =============================================================== -->
        <!-- LIGHTBOX MODAL: PREVIEW FOTO WATERMARK RESOLUSI PENUH -->
        <!-- =============================================================== -->
        <div
            v-if="activePhotoModal.isOpen"
            class="position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3"
            style="background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(6px); z-index: 1070;"
            @click.self="activePhotoModal.isOpen = false"
        >
            <div
                class="card border-0 bg-dark text-white shadow-2xl overflow-hidden w-100"
                style="max-width: 650px; border-radius: 16px; max-height: 92vh; display: flex; flex-direction: column;"
            >
                <!-- Header Lightbox -->
                <div class="px-3 py-2 bg-black bg-opacity-50 d-flex justify-content-between align-items-center border-bottom border-secondary">
                    <div>
                        <h6 class="mb-0 fw-bold fs-6">{{ activePhotoModal.title }}</h6>
                        <small class="text-white-50" style="font-size: 0.75rem;">{{ activePhotoModal.time }}</small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" @click="activePhotoModal.isOpen = false"></button>
                </div>

                <!-- Foto Resolusi Penuh -->
                <div class="p-2 text-center bg-black d-flex align-items-center justify-content-center overflow-auto" style="flex: 1 1 auto; max-height: 65vh;">
                    <img
                        :src="activePhotoModal.url"
                        :alt="activePhotoModal.title"
                        class="img-fluid rounded-2 shadow-sm"
                        style="max-height: 60vh; object-fit: contain;"
                    />
                </div>

                <!-- Footer Info Lokasi -->
                <div class="p-3 bg-dark border-top border-secondary small">
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <i class="bi bi-geo-alt-fill text-danger fs-6 flex-shrink-0 mt-1"></i>
                        <div>
                            <span class="d-block text-white fw-semibold">{{ activePhotoModal.address }}</span>
                            <span v-if="activePhotoModal.lat && activePhotoModal.lng" class="text-white-50" style="font-size: 0.75rem;">
                                GPS: {{ formatCoordinate(activePhotoModal.lat, 6) }}, {{ formatCoordinate(activePhotoModal.lng, 6) }}
                            </span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-50">
                        <a
                            v-if="activePhotoModal.lat && activePhotoModal.lng"
                            :href="`https://www.google.com/maps?q=${activePhotoModal.lat},${activePhotoModal.lng}`"
                            target="_blank"
                            rel="noopener noreferrer"
                            class="btn btn-outline-info btn-sm py-1 px-3"
                        >
                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Google Maps
                        </a>
                        <span v-else></span>
                        <button type="button" class="btn btn-secondary btn-sm px-3" @click="activePhotoModal.isOpen = false">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
@keyframes liveDotPulse {
    0%, 100% {
        opacity: 1;
        transform: scale(1);
    }
    50% {
        opacity: 0.35;
        transform: scale(0.8);
    }
}
.live-dot {
    animation: liveDotPulse 1.5s ease-in-out infinite;
}
@keyframes spinIcon {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
.spin-icon {
    animation: spinIcon 1s linear infinite;
    display: inline-block;
}
.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
