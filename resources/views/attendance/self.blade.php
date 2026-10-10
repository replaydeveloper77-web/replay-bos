@extends('layouts.app')

@section('title', 'Absensi Saya')

@section('content')
    <h5 class="mb-3">Absensi Saya — {{ $employee->name }}</h5>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-4 mb-3" style="max-width: 520px; border-radius: 12px;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 fw-bold"><i class="bi bi-clock text-success me-2"></i>Absensi Hari Ini</h6>
            <span class="badge bg-light text-muted border">{{ now()->translatedFormat('l, d F Y') }}</span>
        </div>

        <div class="mb-3">
            <!-- Check-in Box -->
            <div class="p-3 rounded-3 mb-2 border {{ $today && $today->isCheckedIn() ? 'border-success-subtle bg-success-subtle bg-opacity-10' : 'bg-light' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small fw-semibold d-block text-secondary">Check-in Masuk</span>
                        @if ($today && $today->isCheckedIn())
                            <strong class="text-success fs-6"><i class="bi bi-box-arrow-in-right me-1"></i>{{ $today->check_in_at->format('H:i') }} WIB</strong>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Belum Check-in</span>
                        @endif
                    </div>
                    @if ($today && $today->checkInPhotoUrl())
                        <button type="button" class="btn p-0 border-0" onclick="openSelfLightbox('{{ $today->checkInPhotoUrl() }}', 'Foto Absensi Check-in', '{{ $today->check_in_at->format('H:i') }} WIB', '{{ addslashes($today->check_in_address ?? '-') }}', '{{ $today->check_in_lat }}', '{{ $today->check_in_lng }}')">
                            <img src="{{ $today->checkInPhotoUrl() }}" alt="Foto Check-in" class="rounded-3 shadow-xs border border-success" style="width: 54px; height: 54px; object-fit: cover;">
                        </button>
                    @endif
                </div>
                @if ($today && $today->isCheckedIn() && ($today->check_in_address || $today->check_in_lat))
                    <div class="mt-2 pt-2 border-top border-success-subtle small text-muted">
                        <div class="text-dark mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $today->check_in_address ?: 'Lokasi tercatat' }}</div>
                        @if ($today->check_in_lat && $today->check_in_lng)
                            <a href="https://www.google.com/maps?q={{ $today->check_in_lat }},{{ $today->check_in_lng }}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none small fw-semibold">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Buka Peta ({{ number_format($today->check_in_lat, 4) }}, {{ number_format($today->check_in_lng, 4) }})
                            </a>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Check-out Box -->
            <div class="p-3 rounded-3 mb-2 border {{ $today && $today->isCheckedOut() ? 'border-danger-subtle bg-danger-subtle bg-opacity-10' : 'bg-light' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <span class="small fw-semibold d-block text-secondary">Check-out Pulang</span>
                        @if ($today && $today->isCheckedOut())
                            <strong class="text-danger fs-6"><i class="bi bi-box-arrow-right me-1"></i>{{ $today->check_out_at->format('H:i') }} WIB</strong>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border">Belum Check-out</span>
                        @endif
                    </div>
                    @if ($today && $today->checkOutPhotoUrl())
                        <button type="button" class="btn p-0 border-0" onclick="openSelfLightbox('{{ $today->checkOutPhotoUrl() }}', 'Foto Absensi Check-out', '{{ $today->check_out_at->format('H:i') }} WIB', '{{ addslashes($today->check_out_address ?? '-') }}', '{{ $today->check_out_lat }}', '{{ $today->check_out_lng }}')">
                            <img src="{{ $today->checkOutPhotoUrl() }}" alt="Foto Check-out" class="rounded-3 shadow-xs border border-danger" style="width: 54px; height: 54px; object-fit: cover;">
                        </button>
                    @endif
                </div>
                @if ($today && $today->isCheckedOut() && ($today->check_out_address || $today->check_out_lat))
                    <div class="mt-2 pt-2 border-top border-danger-subtle small text-muted">
                        <div class="text-dark mb-1"><i class="bi bi-geo-alt-fill text-danger me-1"></i>{{ $today->check_out_address ?: 'Lokasi tercatat' }}</div>
                        @if ($today->check_out_lat && $today->check_out_lng)
                            <a href="https://www.google.com/maps?q={{ $today->check_out_lat }},{{ $today->check_out_lng }}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none small fw-semibold">
                                <i class="bi bi-box-arrow-up-right me-1"></i>Buka Peta ({{ number_format($today->check_out_lat, 4) }}, {{ number_format($today->check_out_lng, 4) }})
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        @if (! $today || ! $today->isCheckedIn())
            <button type="button" class="btn btn-success w-100 py-2 fw-semibold shadow-sm" onclick="openSelfAttendanceModal('CHECK-IN')">
                <i class="bi bi-camera-fill me-1"></i> Ambil Foto &amp; Check-in Sekarang
            </button>
            <small class="text-muted d-block text-center mt-2" style="font-size: 0.8rem;">
                <i class="bi bi-shield-lock-fill text-success me-1"></i>Watermark logo Replay, timestamp, dan lokasi otomatis dibubuhkan
            </small>
        @elseif (! $today->isCheckedOut())
            <button type="button" class="btn btn-danger w-100 py-2 fw-semibold shadow-sm" onclick="openSelfAttendanceModal('CHECK-OUT')">
                <i class="bi bi-camera-fill me-1"></i> Ambil Foto &amp; Check-out Sekarang
            </button>
            <small class="text-muted d-block text-center mt-2" style="font-size: 0.8rem;">
                <i class="bi bi-shield-lock-fill text-danger me-1"></i>Watermark Replay BOS otomatis disertakan
            </small>
        @else
            <div class="alert alert-success small mb-0 text-center py-3 border-0 bg-success-subtle text-success-emphasis rounded-3">
                <i class="bi bi-check-circle-fill fs-5 d-block mb-1"></i>
                <strong>Absensi Hari Ini Lengkap!</strong>
                <div class="small">Terima kasih atas kerja keras Anda hari ini.</div>
            </div>
        @endif
    </div>

    <div class="noc-stat-card bg-white p-4" style="border-radius: 12px;">
        <h6 class="mb-3 fw-bold">Riwayat 14 Hari Terakhir</h6>
        @if ($history->isEmpty())
            <p class="text-muted small mb-0">Belum ada riwayat absensi sebelumnya.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($history as $row)
                            <tr>
                                <td>{{ $row->date->translatedFormat('d M Y') }}</td>
                                <td>
                                    <span class="text-success">{{ $row->check_in_at?->format('H:i') ?? '-' }}</span>
                                    @if ($row->checkInPhotoUrl())
                                        <button type="button" class="btn btn-xs btn-outline-success p-0 px-1 border-0" onclick="openSelfLightbox('{{ $row->checkInPhotoUrl() }}', 'Absensi Check-in ({{ $row->date->format('d/m/Y') }})', '{{ $row->check_in_at->format('H:i') }} WIB', '{{ addslashes($row->check_in_address ?? '-') }}', '{{ $row->check_in_lat }}', '{{ $row->check_in_lng }}')">
                                            <i class="bi bi-camera"></i>
                                        </button>
                                    @endif
                                </td>
                                <td>
                                    <span class="text-danger">{{ $row->check_out_at?->format('H:i') ?? '-' }}</span>
                                    @if ($row->checkOutPhotoUrl())
                                        <button type="button" class="btn btn-xs btn-outline-danger p-0 px-1 border-0" onclick="openSelfLightbox('{{ $row->checkOutPhotoUrl() }}', 'Absensi Check-out ({{ $row->date->format('d/m/Y') }})', '{{ $row->check_out_at->format('H:i') }} WIB', '{{ addslashes($row->check_out_address ?? '-') }}', '{{ $row->check_out_lat }}', '{{ $row->check_out_lng }}')">
                                            <i class="bi bi-camera"></i>
                                        </button>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $row->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>

    {{-- MODAL KAMERA LIVE SELF --}}
    <div id="selfAttModal" class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3" style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1060;">
        <div class="card border-0 shadow-lg bg-white overflow-hidden w-100" style="max-width: 520px; border-radius: 14px; max-height: 94vh;">
            <!-- 1. Header Modal -->
            <div class="px-4 py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-2">
                    <img src="{{ asset('images/branding/logo-replay-net.png') }}" alt="Replay" style="width: 28px; height: 28px; object-fit: contain;">
                    <h6 id="selfModalTitle" class="mb-0 fw-semibold text-dark fs-6">Absensi Check-in</h6>
                </div>
                <button type="button" class="btn-close" aria-label="Tutup" onclick="closeSelfAttendanceModal()"></button>
            </div>
            <!-- 2. Body Modal -->
            <div class="p-3 overflow-y-auto" style="max-height: 72vh;">
                <!-- Lokasi GPS -->
                <div class="p-3 mb-3 rounded-3 bg-light border d-flex align-items-start justify-content-between gap-2">
                    <div class="d-flex align-items-start gap-2 flex-grow-1 min-w-0">
                        <i class="bi bi-geo-alt text-success fs-5 flex-shrink-0 mt-0.5"></i>
                        <div class="min-w-0 flex-grow-1">
                            <strong id="selfGeoAddress" class="d-block text-dark fw-semibold text-truncate" style="font-size: 0.825rem; line-height: 1.35;">Mendeteksi lokasi...</strong>
                            <span id="selfGeoCoords" class="text-muted d-block mt-0.5" style="font-size: 0.75rem;">GPS: Belum tersedia</span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 flex-shrink-0 ms-2" style="font-size: 0.75rem;" onclick="refreshSelfLocation()" title="Segarkan Lokasi">
                        <i class="bi bi-arrow-clockwise"></i>
                    </button>
                </div>

                <!-- Area Kamera & Preview -->
                <div class="position-relative rounded-3 overflow-hidden text-center mb-3" style="aspect-ratio: 4/3; max-height: 380px; background-color: #0f172a;">
                    <div id="selfVideoBox" class="w-100 h-100 position-relative d-flex align-items-center justify-content-center">
                        <video id="selfVideo" autoplay playsinline muted class="w-100 h-100" style="object-fit: cover; transform: scaleX(-1);"></video>
                        <div class="position-absolute top-0 start-0 m-3 px-2 py-1 rounded-2 d-flex align-items-center gap-1.5 shadow-sm" style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px);">
                            <span class="bg-danger rounded-circle d-inline-block" style="width: 7px; height: 7px;"></span>
                            <span class="text-white fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">LIVE</span>
                        </div>
                    </div>
                    <div id="selfPhotoBox" class="d-none w-100 h-100 position-relative bg-black">
                        <img id="selfCapturedImg" src="" alt="Hasil Foto Absensi" class="w-100 h-100" style="object-fit: contain;">
                    </div>
                </div>

                <!-- Tombol Ambil Foto / Ambil Ulang -->
                <div class="mb-3">
                    <button type="button" id="selfBtnSnap" class="btn btn-success w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm" onclick="takeSelfSnapshot()">
                        <i class="bi bi-camera-fill"></i>
                        <span>Ambil Foto</span>
                    </button>
                    <button type="button" id="selfBtnRetake" class="btn btn-outline-secondary w-100 py-2 fw-semibold d-none d-flex align-items-center justify-content-center gap-2" onclick="retakeSelfSnapshot()">
                        <i class="bi bi-arrow-repeat"></i>
                        <span>Ambil Ulang Foto</span>
                    </button>
                </div>

                <form id="selfForm" method="POST" action="">
                    @csrf
                    <input type="hidden" name="lat" id="selfFormLat">
                    <input type="hidden" name="lng" id="selfFormLng">
                    <input type="hidden" name="address" id="selfFormAddress">
                    <input type="hidden" name="photo" id="selfFormPhoto">
                    <div>
                        <label class="form-label small fw-semibold text-secondary mb-1">Catatan Kerja (Opsional)</label>
                        <textarea name="notes" id="selfFormNotes" class="form-control form-control-sm" rows="2" placeholder="Tuliskan catatan kerja jika ada..."></textarea>
                    </div>
                </form>
            </div>
            <!-- 3. Footer Modal -->
            <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center">
                <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="closeSelfAttendanceModal()">Batal</button>
                <button type="button" id="selfBtnSubmit" class="btn btn-sm btn-success px-4 py-1.5 fw-semibold text-white shadow-sm" disabled onclick="submitSelfAttendance()">
                    <i class="bi bi-check-lg me-1"></i> Simpan Absensi
                </button>
            </div>
        </div>
    </div>

    {{-- LIGHTBOX MODAL SELF --}}
    <div id="selfLightbox" class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3" style="background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(6px); z-index: 1070;" onclick="if(event.target.id==='selfLightbox') document.getElementById('selfLightbox').classList.add('d-none')">
        <div class="card border-0 bg-dark text-white shadow-2xl overflow-hidden w-100" style="max-width: 650px; border-radius: 16px; max-height: 92vh;">
            <div class="px-3 py-2 bg-black bg-opacity-50 d-flex justify-content-between align-items-center border-bottom border-secondary">
                <div>
                    <h6 id="selfLbTitle" class="mb-0 fw-bold fs-6">Foto Absensi</h6>
                    <small id="selfLbTime" class="text-white-50" style="font-size: 0.75rem;"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('selfLightbox').classList.add('d-none')"></button>
            </div>
            <div class="p-2 text-center bg-black d-flex align-items-center justify-content-center overflow-auto" style="max-height: 65vh;">
                <img id="selfLbImg" src="" alt="Foto Absensi" class="img-fluid rounded-2" style="max-height: 60vh; object-fit: contain;">
            </div>
            <div class="p-3 bg-dark border-top border-secondary small">
                <div class="d-flex align-items-start gap-2 mb-2">
                    <i class="bi bi-geo-alt-fill text-danger fs-6 flex-shrink-0 mt-1"></i>
                    <div>
                        <span id="selfLbAddress" class="d-block text-white fw-semibold"></span>
                        <span id="selfLbCoords" class="text-white-50" style="font-size: 0.75rem;"></span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-50">
                    <a id="selfLbMapLink" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-outline-info btn-sm py-1 px-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Google Maps
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm px-3" onclick="document.getElementById('selfLightbox').classList.add('d-none')">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    let selfStream = null;
    let selfMode = 'CHECK-IN';
    let selfLat = null;
    let selfLng = null;
    let selfAddress = '';

    const selfEmpInfo = {
        name: "{{ addslashes($employee->name) }}",
        number: "{{ addslashes($employee->employee_number) }}",
        position: "{{ addslashes($employee->position ?: 'Staf') }}"
    };

    function refreshSelfLocation() {
        const addressEl = document.getElementById('selfGeoAddress');
        const coordsEl = document.getElementById('selfGeoCoords');
        if (!navigator.geolocation) {
            if (addressEl) addressEl.textContent = 'Geolocation tidak didukung browser ini';
            return;
        }
        if (addressEl) addressEl.textContent = 'Mendeteksi lokasi GPS...';
        navigator.geolocation.getCurrentPosition(
            async (pos) => {
                selfLat = pos.coords.latitude;
                selfLng = pos.coords.longitude;
                if (coordsEl) coordsEl.textContent = `GPS: ${selfLat.toFixed(6)}, ${selfLng.toFixed(6)}`;
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${selfLat}&lon=${selfLng}&zoom=18&addressdetails=1`, { headers: { 'Accept': 'application/json' } });
                    if (res.ok) {
                        const data = await res.json();
                        selfAddress = data.display_name || `Koordinat: ${selfLat.toFixed(6)}, ${selfLng.toFixed(6)}`;
                    } else {
                        selfAddress = `Koordinat: ${selfLat.toFixed(6)}, ${selfLng.toFixed(6)}`;
                    }
                } catch {
                    selfAddress = `Koordinat: ${selfLat.toFixed(6)}, ${selfLng.toFixed(6)}`;
                }
                if (addressEl) addressEl.textContent = selfAddress;
            },
            () => {
                if (addressEl) addressEl.textContent = 'Izin lokasi tidak diberikan / tidak dapat diambil';
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    async function startSelfCamera() {
        try {
            const video = document.getElementById('selfVideo');
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return;
            selfStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
            });
            video.srcObject = selfStream;
            video.play();
        } catch (e) {
            console.warn('Camera access error:', e);
        }
    }

    function stopSelfCamera() {
        if (selfStream) {
            selfStream.getTracks().forEach(t => t.stop());
            selfStream = null;
        }
    }

    function openSelfAttendanceModal(mode) {
        selfMode = mode;
        const modal = document.getElementById('selfAttModal');
        const title = document.getElementById('selfModalTitle');
        const header = document.getElementById('selfModalHeader');
        const form = document.getElementById('selfForm');
        const btnConfirm = document.getElementById('selfBtnSubmit');

        modal.classList.remove('d-none');
        retakeSelfSnapshot();
        refreshSelfLocation();
        startSelfCamera();

        if (mode === 'CHECK-IN') {
            title.textContent = 'Absensi Check-in';
            form.action = "{{ route('attendance.check-in') }}";
            btnConfirm.className = 'btn btn-sm btn-success px-4 py-1.5 fw-semibold text-white shadow-sm';
            btnConfirm.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Check-in';
        } else {
            title.textContent = 'Absensi Check-out';
            form.action = "{{ route('attendance.check-out') }}";
            btnConfirm.className = 'btn btn-sm btn-danger px-4 py-1.5 fw-semibold text-white shadow-sm';
            btnConfirm.innerHTML = '<i class="bi bi-check-lg me-1"></i> Simpan Check-out';
        }
    }

    function closeSelfAttendanceModal() {
        stopSelfCamera();
        document.getElementById('selfAttModal').classList.add('d-none');
    }

    function loadSelfWatermarkLogo() {
        return new Promise(resolve => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => resolve(img);
            img.onerror = () => resolve(null);
            img.src = '/images/branding/logo-replay-net.png';
        });
    }

    async function renderSelfWatermark(source, isMirror) {
        const canvas = document.createElement('canvas');
        const w = 960;
        let h = 720;
        if (source.videoWidth && source.videoHeight) {
            h = Math.round((source.videoHeight / source.videoWidth) * w) || 720;
        }
        canvas.width = w;
        canvas.height = h;
        const ctx = canvas.getContext('2d');

        if (isMirror) {
            ctx.save();
            ctx.translate(w, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(source, 0, 0, w, h);
            ctx.restore();
        } else {
            ctx.drawImage(source, 0, 0, w, h);
        }

        // Format Waktu WIB Aktual
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
        const partMap = {};
        for (const p of parts) partMap[p.type] = p.value;
        const dayName = partMap.weekday || 'Minggu';
        const dayDate = partMap.day || String(snapTime.getDate()).padStart(2, '0');
        const monthName = partMap.month || 'Januari';
        const yearNum = partMap.year || String(snapTime.getFullYear());
        const hours = partMap.hour || String(snapTime.getHours()).padStart(2, '0');
        const mins = partMap.minute || String(snapTime.getMinutes()).padStart(2, '0');
        const secs = partMap.second || String(snapTime.getSeconds()).padStart(2, '0');
        const formattedTime = `${dayName}, ${dayDate} ${monthName} ${yearNum} · ${hours}:${mins}:${secs} WIB`;

        // Format Alamat & GPS
        let addrText = selfAddress ? selfAddress.trim() : '';
        if (!addrText) {
            addrText = selfLat !== null ? 'Alamat belum ditemukan' : 'Lokasi belum terdeteksi';
        }

        let gpsText = '';
        if (selfLat !== null && selfLng !== null) {
            gpsText = `GPS: ${selfLat.toFixed(6)}, ${selfLng.toFixed(6)}`;
        } else {
            gpsText = 'GPS: Belum tersedia';
        }

        // Overlay Bawah Semi-Transparan
        const overlayHeight = 116;
        const bottomGrad = ctx.createLinearGradient(0, h - overlayHeight - 20, 0, h);
        bottomGrad.addColorStop(0, 'rgba(0, 0, 0, 0)');
        bottomGrad.addColorStop(0.2, 'rgba(15, 23, 42, 0.78)');
        bottomGrad.addColorStop(1, 'rgba(15, 23, 42, 0.94)');
        ctx.fillStyle = bottomGrad;
        ctx.fillRect(0, h - overlayHeight - 20, w, overlayHeight + 20);

        // Logo Resmi Replay
        const logo = await loadSelfWatermarkLogo();
        const logoSize = 64;
        const logoX = 24;
        const logoY = h - 94;
        if (logo) ctx.drawImage(logo, logoX, logoY, logoSize, logoSize);

        // Watermark Teks
        const textX = logoX + logoSize + 16;
        const maxTextWidth = w - textX - 24;

        function truncateText(text, maxWidth) {
            if (ctx.measureText(text).width <= maxWidth) return text;
            let truncated = text;
            while (truncated.length > 0 && ctx.measureText(truncated + '...').width > maxWidth) {
                truncated = truncated.slice(0, -1);
            }
            return truncated + '...';
        }

        // Baris 1: PT. Replay Inti Media
        ctx.fillStyle = '#ffffff';
        ctx.font = 'bold 16px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillText('PT. Replay Inti Media', textX, h - 74);

        // Baris 2: Waktu WIB
        ctx.fillStyle = '#f1f5f9';
        ctx.font = '500 13px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillText(formattedTime, textX, h - 54);

        // Baris 3: Alamat
        ctx.fillStyle = '#e2e8f0';
        ctx.font = '12px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillText(truncateText(addrText, maxTextWidth), textX, h - 36);

        // Baris 4: GPS
        ctx.fillStyle = '#cbd5e1';
        ctx.font = '12px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif';
        ctx.fillText(gpsText, textX, h - 18);

        return canvas.toDataURL('image/jpeg', 0.90);
    }

    async function takeSelfSnapshot() {
        const video = document.getElementById('selfVideo');
        const dataUrl = await renderSelfWatermark(video, true);
        applySelfSnapshotResult(dataUrl);
    }

    function applySelfSnapshotResult(dataUrl) {
        stopSelfCamera();
        document.getElementById('selfVideoBox').classList.add('d-none');
        document.getElementById('selfPhotoBox').classList.remove('d-none');
        document.getElementById('selfCapturedImg').src = dataUrl;
        document.getElementById('selfBtnSnap').classList.add('d-none');
        document.getElementById('selfBtnRetake').classList.remove('d-none');
        document.getElementById('selfBtnSubmit').removeAttribute('disabled');
        document.getElementById('selfFormPhoto').value = dataUrl;
    }

    function retakeSelfSnapshot() {
        document.getElementById('selfVideoBox').classList.remove('d-none');
        document.getElementById('selfPhotoBox').classList.add('d-none');
        document.getElementById('selfCapturedImg').src = '';
        document.getElementById('selfBtnSnap').classList.remove('d-none');
        document.getElementById('selfBtnRetake').classList.add('d-none');
        document.getElementById('selfBtnSubmit').setAttribute('disabled', 'disabled');
        document.getElementById('selfFormPhoto').value = '';
        startSelfCamera();
    }

    function submitSelfAttendance() {
        document.getElementById('selfFormLat').value = selfLat || '';
        document.getElementById('selfFormLng').value = selfLng || '';
        document.getElementById('selfFormAddress').value = selfAddress || '';
        const btn = document.getElementById('selfBtnSubmit');
        btn.setAttribute('disabled', 'disabled');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        document.getElementById('selfForm').submit();
    }

    function openSelfLightbox(url, title, time, address, lat, lng) {
        const lb = document.getElementById('selfLightbox');
        document.getElementById('selfLbImg').src = url;
        document.getElementById('selfLbTitle').textContent = title;
        document.getElementById('selfLbTime').textContent = time;
        document.getElementById('selfLbAddress').textContent = address;
        const coordsEl = document.getElementById('selfLbCoords');
        const mapLink = document.getElementById('selfLbMapLink');
        if (lat && lng && lat !== 'null' && lng !== 'null') {
            coordsEl.textContent = `GPS: ${Number(lat).toFixed(6)}, ${Number(lng).toFixed(6)}`;
            mapLink.href = `https://www.google.com/maps?q=${lat},${lng}`;
            mapLink.classList.remove('d-none');
        } else {
            coordsEl.textContent = '';
            mapLink.classList.add('d-none');
        }
        lb.classList.remove('d-none');
    }

    (function () {
        refreshSelfLocation();
    })();
</script>
@endpush
