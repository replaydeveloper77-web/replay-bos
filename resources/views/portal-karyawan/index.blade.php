@extends('layouts.app')

@section('title', 'Portal Karyawan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0 fw-bold">Portal Karyawan</h5>
            <small class="text-muted">Data Diri & Layanan Mandiri Karyawan</small>
        </div>
        <div>
            @if ($user->hasRole('super-admin') || $user->hasRole('hr'))
                <a href="{{ route('employees.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-people me-1"></i> Buka Data Karyawan (Semua)
                </a>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (! $employee)
        {{-- Kondisi Akun Belum Ditautkan ke Karyawan --}}
        <div class="card border-0 shadow-sm p-4 text-center my-4" style="max-width: 600px; margin: 0 auto; border-radius: 12px;">
            <div class="my-3">
                <i class="bi bi-person-x text-muted" style="font-size: 3.5rem;"></i>
            </div>
            <h5 class="fw-bold mb-2">Akun Belum Ditautkan ke Data Karyawan</h5>
            <p class="text-muted small mb-4">
                Akun login Anda (<strong>{{ $user->name }}</strong> / <code>{{ $user->username }}</code>) saat ini belum terhubung dengan master data karyawan mana pun di sistem.
            </p>
            @if ($user->hasRole('super-admin') || $user->hasRole('hr'))
                <div>
                    <a href="{{ route('employees.index') }}" class="btn btn-primary btn-sm px-3">
                        <i class="bi bi-link-45deg me-1"></i> Tautkan Akun di Menu Data Karyawan
                    </a>
                </div>
            @else
                <div class="alert alert-light border small text-muted mb-0">
                    Silakan hubungi bagian <strong>HR</strong> atau <strong>Super Admin</strong> untuk menautkan akun Anda ke data karyawan.
                </div>
            @endif
        </div>
    @else
        {{-- KARTU PROFIL UTAMA DATA DIRI KARYAWAN --}}
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
            <div class="p-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); color: #fff;">
                <div class="d-flex flex-wrap align-items-center gap-4">
                    <div class="position-relative">
                        <img src="{{ $employee->photoUrl() }}" alt="{{ $employee->name }}" class="rounded shadow-sm border border-3 border-white bg-white" style="width: 105px; height: 140px; object-fit: cover;">
                        @if ($employee->is_active)
                            <span class="position-absolute bottom-0 end-0 badge bg-success border border-white rounded-pill px-2 py-1 shadow-xs" style="transform: translate(20%, 20%); font-size: 0.7rem;">
                                <i class="bi bi-check-circle-fill"></i> Aktif
                            </span>
                        @else
                            <span class="position-absolute bottom-0 end-0 badge bg-danger border border-white rounded-pill px-2 py-1 shadow-xs" style="transform: translate(20%, 20%); font-size: 0.7rem;">
                                Nonaktif
                            </span>
                        @endif
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                            <h4 class="mb-0 fw-bold">{{ $employee->name }}</h4>
                            <span class="badge bg-white text-primary fw-semibold px-2 py-1"><code>{{ $employee->employee_number }}</code></span>
                        </div>
                        <p class="mb-2 text-white-50 fs-6">
                            {{ $employee->position ?: 'Staf' }} • Departemen {{ ucfirst(strtolower($employee->department)) }}
                        </p>
                        <div class="d-flex flex-wrap gap-3 small text-white-75">
                            @if ($employee->email)
                                <span><i class="bi bi-envelope me-1"></i> {{ $employee->email }}</span>
                            @endif
                            @if ($employee->phone)
                                <span><i class="bi bi-whatsapp me-1"></i> {{ $employee->phone }}</span>
                            @endif
                            @if ($employee->join_date)
                                <span><i class="bi bi-calendar-event me-1"></i> Masuk: {{ $employee->join_date->format('d/m/Y') }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- DETAIL LENGKAP DATA DIRI --}}
            <div class="card-body p-4 bg-white">
                <div class="row g-4">
                    {{-- Informasi Kepegawaian & BPJS --}}
                    <div class="col-lg-6 border-end">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-shield-check text-primary me-2"></i>Informasi Kepegawaian & BPJS
                        </h6>
                        <div class="row g-3 small">
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nomor Induk Karyawan</span>
                                <strong class="text-dark">{{ $employee->employee_number }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Jabatan</span>
                                <strong class="text-dark">{{ $employee->position ?: '-' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Departemen</span>
                                <strong class="text-dark">{{ ucfirst(strtolower($employee->department)) }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Status Kepegawaian</span>
                                <strong class="text-dark">{{ ucfirst(strtolower($employee->employment_status)) }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nomor BPJS Ketenagakerjaan</span>
                                <strong class="text-primary fs-6">{{ $employee->no_jmo ?: '-' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nomor BPJS Kesehatan</span>
                                <strong class="text-primary fs-6">{{ $employee->no_kpj ?: '-' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Tahun / Tanggal Masuk</span>
                                <strong class="text-dark">{{ $employee->join_date ? $employee->join_date->format('d F Y') : '-' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Akhir Bekerja</span>
                                <strong class="text-dark">{{ $employee->resign_date ? $employee->resign_date->format('d F Y') : ($employee->contract_end_date ? $employee->contract_end_date->format('d F Y') : '-') }}</strong>
                            </div>
                        </div>
                    </div>

                    {{-- Data Identitas Diri --}}
                    <div class="col-lg-6">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-person-lines-fill text-primary me-2"></i>Data Identitas Pribadi
                        </h6>
                        <div class="row g-3 small">
                            <div class="col-sm-6">
                                <span class="text-muted d-block">NIK KTP</span>
                                <strong class="text-dark">{{ $employee->nik_ktp ?: '-' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Jenis Kelamin</span>
                                <strong class="text-dark">{{ $employee->gender === 'L' ? 'Laki-laki' : ($employee->gender === 'P' ? 'Perempuan' : '-') }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Tempat, Tanggal Lahir</span>
                                <strong class="text-dark">{{ $employee->birth_place ?: '-' }}@if($employee->birth_date), {{ $employee->birth_date->format('d/m/Y') }}@endif</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nomor Telepon / WA</span>
                                <strong class="text-dark">{{ $employee->phone ?: '-' }}</strong>
                            </div>
                            <div class="col-12">
                                <span class="text-muted d-block">Email</span>
                                <strong class="text-dark">{{ $employee->email ?: '-' }}</strong>
                            </div>
                            <div class="col-12">
                                <span class="text-muted d-block">Alamat Tempat Tinggal</span>
                                <div class="text-dark">{{ $employee->address ?: '-' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- WIDGET LAYANAN MANDIRI: ABSENSI & CUTI PRIBADI --}}
        <div class="row g-3">
            {{-- Absensi Hari Ini --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-clock text-success me-2"></i>Absensi Hari Ini</h6>
                        <span class="badge bg-light text-muted border">{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <div class="mb-3">
                        <!-- Detail Check-in -->
                        <div class="p-3 rounded-3 mb-2 border {{ $today && $today->isCheckedIn() ? 'border-success-subtle bg-success-subtle bg-opacity-10' : 'bg-light' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-in-right fs-5 {{ $today && $today->isCheckedIn() ? 'text-success' : 'text-muted' }}"></i>
                                    <div>
                                        <span class="small fw-semibold d-block text-secondary">Check-in Masuk</span>
                                        @if ($today && $today->isCheckedIn())
                                            <strong class="text-success fs-6">{{ $today->check_in_at->format('H:i') }} WIB</strong>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border">Belum Check-in</span>
                                        @endif
                                    </div>
                                </div>
                                @if ($today && $today->checkInPhotoUrl())
                                    <div class="position-relative">
                                        <button type="button" class="btn p-0 border-0" onclick="openLightbox('{{ $today->checkInPhotoUrl() }}', 'Foto Absensi Check-in', '{{ $today->check_in_at->format('H:i') }} WIB', '{{ addslashes($today->check_in_address ?? '-') }}', '{{ $today->check_in_lat }}', '{{ $today->check_in_lng }}')">
                                            <img src="{{ $today->checkInPhotoUrl() }}" alt="Foto Check-in" class="rounded-3 shadow-xs border border-success" style="width: 58px; height: 58px; object-fit: cover;">
                                            <span class="position-absolute bottom-0 end-0 badge bg-dark bg-opacity-75 p-1" style="font-size: 0.65rem;">
                                                <i class="bi bi-zoom-in text-white"></i>
                                            </span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            @if ($today && $today->isCheckedIn() && ($today->check_in_address || $today->check_in_lat))
                                <div class="mt-2 pt-2 border-top border-success-subtle small text-muted">
                                    <div class="d-flex align-items-start gap-1 mb-1">
                                        <i class="bi bi-geo-alt-fill text-danger flex-shrink-0 mt-1"></i>
                                        <span class="text-dark">{{ $today->check_in_address ?: 'Alamat tersimpan' }}</span>
                                    </div>
                                    @if ($today->check_in_lat && $today->check_in_lng)
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-secondary border">GPS: {{ number_format($today->check_in_lat, 5) }}, {{ number_format($today->check_in_lng, 5) }}</span>
                                            <a href="https://www.google.com/maps?q={{ $today->check_in_lat }},{{ $today->check_in_lng }}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none small fw-semibold">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Buka Peta
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Detail Check-out -->
                        <div class="p-3 rounded-3 mb-2 border {{ $today && $today->isCheckedOut() ? 'border-danger-subtle bg-danger-subtle bg-opacity-10' : 'bg-light' }}">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-2">
                                    <i class="bi bi-box-arrow-right fs-5 {{ $today && $today->isCheckedOut() ? 'text-danger' : 'text-muted' }}"></i>
                                    <div>
                                        <span class="small fw-semibold d-block text-secondary">Check-out Pulang</span>
                                        @if ($today && $today->isCheckedOut())
                                            <strong class="text-danger fs-6">{{ $today->check_out_at->format('H:i') }} WIB</strong>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border">Belum Check-out</span>
                                        @endif
                                    </div>
                                </div>
                                @if ($today && $today->checkOutPhotoUrl())
                                    <div class="position-relative">
                                        <button type="button" class="btn p-0 border-0" onclick="openLightbox('{{ $today->checkOutPhotoUrl() }}', 'Foto Absensi Check-out', '{{ $today->check_out_at->format('H:i') }} WIB', '{{ addslashes($today->check_out_address ?? '-') }}', '{{ $today->check_out_lat }}', '{{ $today->check_out_lng }}')">
                                            <img src="{{ $today->checkOutPhotoUrl() }}" alt="Foto Check-out" class="rounded-3 shadow-xs border border-danger" style="width: 58px; height: 58px; object-fit: cover;">
                                            <span class="position-absolute bottom-0 end-0 badge bg-dark bg-opacity-75 p-1" style="font-size: 0.65rem;">
                                                <i class="bi bi-zoom-in text-white"></i>
                                            </span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            @if ($today && $today->isCheckedOut() && ($today->check_out_address || $today->check_out_lat))
                                <div class="mt-2 pt-2 border-top border-danger-subtle small text-muted">
                                    <div class="d-flex align-items-start gap-1 mb-1">
                                        <i class="bi bi-geo-alt-fill text-danger flex-shrink-0 mt-1"></i>
                                        <span class="text-dark">{{ $today->check_out_address ?: 'Alamat tersimpan' }}</span>
                                    </div>
                                    @if ($today->check_out_lat && $today->check_out_lng)
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-secondary border">GPS: {{ number_format($today->check_out_lat, 5) }}, {{ number_format($today->check_out_lng, 5) }}</span>
                                            <a href="https://www.google.com/maps?q={{ $today->check_out_lat }},{{ $today->check_out_lng }}" target="_blank" rel="noopener noreferrer" class="text-primary text-decoration-none small fw-semibold">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Buka Peta
                                            </a>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    @if (! $today || ! $today->isCheckedIn())
                        <button type="button" class="btn btn-success w-100 py-2 fw-semibold shadow-sm" onclick="openAttendanceModal('CHECK-IN')">
                            <i class="bi bi-camera-fill me-1"></i> Ambil Foto &amp; Check-in Sekarang
                        </button>
                        <small class="text-muted d-block text-center mt-2" style="font-size: 0.8rem;">
                            <i class="bi bi-shield-lock-fill text-success me-1"></i>Foto dilengkapi watermark logo Replay, waktu real-time, dan GPS
                        </small>
                    @elseif (! $today->isCheckedOut())
                        <button type="button" class="btn btn-danger w-100 py-2 fw-semibold shadow-sm" onclick="openAttendanceModal('CHECK-OUT')">
                            <i class="bi bi-camera-fill me-1"></i> Ambil Foto &amp; Check-out Sekarang
                        </button>
                        <small class="text-muted d-block text-center mt-2" style="font-size: 0.8rem;">
                            <i class="bi bi-shield-lock-fill text-danger me-1"></i>Foto pulang dibubuhkan watermark resmi Replay BOS
                        </small>
                    @else
                        <div class="alert alert-success small mb-0 text-center py-3 border-0 bg-success-subtle text-success-emphasis rounded-3">
                            <i class="bi bi-check-circle-fill fs-5 d-block mb-1"></i>
                            <strong>Absensi Hari Ini Lengkap!</strong>
                            <div class="small">Terima kasih atas dedikasi dan kerja keras Anda hari ini.</div>
                        </div>
                    @endif

                    @if ($attendanceHistory->isNotEmpty())
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="small fw-semibold text-muted mb-2">Riwayat Absensi 7 Hari Terakhir:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless small mb-0 align-middle">
                                    <tbody>
                                        @foreach ($attendanceHistory as $att)
                                            <tr class="border-bottom-subtle">
                                                <td class="text-muted py-2">{{ $att->date->format('d/m/Y') }}</td>
                                                <td class="py-2">
                                                    <span class="text-success"><i class="bi bi-arrow-down-right"></i> {{ $att->check_in_at ? $att->check_in_at->format('H:i') : '-' }}</span>
                                                    @if ($att->checkInPhotoUrl())
                                                        <button type="button" class="btn btn-xs btn-outline-success p-0 px-1 border-0" onclick="openLightbox('{{ $att->checkInPhotoUrl() }}', 'Absensi Check-in ({{ $att->date->format('d/m/Y') }})', '{{ $att->check_in_at->format('H:i') }} WIB', '{{ addslashes($att->check_in_address ?? '-') }}', '{{ $att->check_in_lat }}', '{{ $att->check_in_lng }}')">
                                                            <i class="bi bi-camera"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                                <td class="py-2">
                                                    <span class="text-danger"><i class="bi bi-arrow-up-right"></i> {{ $att->check_out_at ? $att->check_out_at->format('H:i') : '-' }}</span>
                                                    @if ($att->checkOutPhotoUrl())
                                                        <button type="button" class="btn btn-xs btn-outline-danger p-0 px-1 border-0" onclick="openLightbox('{{ $att->checkOutPhotoUrl() }}', 'Absensi Check-out ({{ $att->date->format('d/m/Y') }})', '{{ $att->check_out_at->format('H:i') }} WIB', '{{ addslashes($att->check_out_address ?? '-') }}', '{{ $att->check_out_lat }}', '{{ $att->check_out_lng }}')">
                                                            <i class="bi bi-camera"></i>
                                                        </button>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Kuota Cuti & Pengajuan Cuti Pribadi --}}
            <div class="col-lg-6">
                <div class="card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 12px;">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0"><i class="bi bi-calendar-check text-primary me-2"></i>Cuti & Izin</h6>
                        <a href="{{ route('leave-requests.self') }}" class="btn btn-primary btn-sm py-1 px-3">
                            <i class="bi bi-plus-lg me-1"></i> Ajukan Cuti/Izin
                        </a>
                    </div>

                    @if ($employee->employment_status === 'TETAP' && $leaveQuota)
                        <div class="bg-light p-3 rounded mb-3">
                            <div class="small fw-semibold text-muted mb-2">Kuota Cuti Tahunan {{ now()->year }}:</div>
                            <div class="d-flex justify-content-between align-items-center text-center">
                                <div>
                                    <div class="fs-4 fw-bold text-dark">{{ $leaveQuota['quota'] }}</div>
                                    <small class="text-muted">Total Hak</small>
                                </div>
                                <div class="border-start ps-3">
                                    <div class="fs-4 fw-bold text-danger">{{ $leaveQuota['used'] }}</div>
                                    <small class="text-muted">Terpakai</small>
                                </div>
                                <div class="border-start ps-3">
                                    <div class="fs-4 fw-bold text-success">{{ $leaveQuota['remaining'] }}</div>
                                    <small class="text-muted">Sisa Kuota</small>
                                </div>
                            </div>
                        </div>
                    @endif

                    <h6 class="small fw-semibold text-muted mb-2">Pengajuan Cuti/Izin Terakhir:</h6>
                    @if ($leaveRequests->isEmpty())
                        <p class="text-muted small py-3 text-center mb-0">Belum ada riwayat pengajuan cuti/izin.</p>
                    @else
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0 small">
                                <thead class="table-light">
                                    <tr>
                                        <th>Jenis</th>
                                        <th>Periode</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($leaveRequests as $lr)
                                        <tr>
                                            <td><strong>{{ $lr->typeLabel() }}</strong></td>
                                            <td class="text-nowrap">{{ $lr->start_date->format('d/m') }} - {{ $lr->end_date->format('d/m/Y') }}</td>
                                            <td>
                                                <span class="badge bg-{{ $lr->status === 'APPROVED' ? 'success' : ($lr->status === 'REJECTED' ? 'danger' : ($lr->status === 'CANCELLED' ? 'secondary' : 'warning text-dark')) }}">
                                                    {{ $lr->statusLabel() }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- MODAL KAMERA ABSENSI BLADE --}}
        <div id="attendanceModal" class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3" style="background: rgba(15, 23, 42, 0.6); backdrop-filter: blur(4px); z-index: 1060;">
            <div class="card border-0 shadow-lg bg-white overflow-hidden w-100" style="max-width: 520px; border-radius: 14px; max-height: 94vh;">
                <!-- 1. Header Modal -->
                <div class="px-4 py-3 bg-white border-bottom d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('images/branding/logo-replay-net.png') }}" alt="Replay" style="width: 28px; height: 28px; object-fit: contain;">
                        <h6 id="modalTitle" class="mb-0 fw-semibold text-dark fs-6">Absensi Check-in</h6>
                    </div>
                    <button type="button" class="btn-close" aria-label="Tutup" onclick="closeAttendanceModal()"></button>
                </div>
                <!-- 2. Body Modal -->
                <div class="p-3 overflow-y-auto" style="max-height: 72vh;">
                    <!-- Lokasi GPS -->
                    <div class="p-3 mb-3 rounded-3 bg-light border d-flex align-items-start justify-content-between gap-2">
                        <div class="d-flex align-items-start gap-2 flex-grow-1 min-w-0">
                            <i class="bi bi-geo-alt text-success fs-5 flex-shrink-0 mt-0.5"></i>
                            <div class="min-w-0 flex-grow-1">
                                <strong id="geoAddress" class="d-block text-dark fw-semibold text-truncate" style="font-size: 0.825rem; line-height: 1.35;">Mendeteksi lokasi...</strong>
                                <span id="geoCoords" class="text-muted d-block mt-0.5" style="font-size: 0.75rem;">GPS: Belum tersedia</span>
                            </div>
                        </div>
                        <button type="button" class="btn btn-outline-secondary btn-sm py-1 px-2 flex-shrink-0 ms-2" style="font-size: 0.75rem;" onclick="refreshBladeLocation()" title="Segarkan Lokasi">
                            <i class="bi bi-arrow-clockwise"></i>
                        </button>
                    </div>

                    <!-- Area Kamera & Preview -->
                    <div class="position-relative rounded-3 overflow-hidden text-center mb-3" style="aspect-ratio: 4/3; max-height: 380px; background-color: #0f172a;">
                        <div id="videoContainer" class="w-100 h-100 position-relative d-flex align-items-center justify-content-center">
                            <video id="bladeVideo" autoplay playsinline muted class="w-100 h-100" style="object-fit: cover; transform: scaleX(-1);"></video>
                            <div class="position-absolute top-0 start-0 m-3 px-2 py-1 rounded-2 d-flex align-items-center gap-1.5 shadow-sm" style="background: rgba(15, 23, 42, 0.75); backdrop-filter: blur(4px);">
                                <span class="bg-danger rounded-circle d-inline-block" style="width: 7px; height: 7px;"></span>
                                <span class="text-white fw-semibold" style="font-size: 10px; letter-spacing: 0.5px;">LIVE</span>
                            </div>
                        </div>
                        <div id="photoPreviewContainer" class="d-none w-100 h-100 position-relative bg-black">
                            <img id="bladeCapturedImg" src="" alt="Hasil Foto Absensi" class="w-100 h-100" style="object-fit: contain;">
                        </div>
                    </div>

                    <!-- Tombol Ambil Foto / Ambil Ulang -->
                    <div class="mb-3">
                        <button type="button" id="btnSnap" class="btn btn-success w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-2 shadow-sm" onclick="takeBladeSnapshot()">
                            <i class="bi bi-camera-fill"></i>
                            <span>Ambil Foto</span>
                        </button>
                        <button type="button" id="btnRetake" class="btn btn-outline-secondary w-100 py-2 fw-semibold d-none d-flex align-items-center justify-content-center gap-2" onclick="retakeBladeSnapshot()">
                            <i class="bi bi-arrow-repeat"></i>
                            <span>Ambil Ulang Foto</span>
                        </button>
                    </div>

                    <form id="attendanceActionForm" method="POST" action="">
                        @csrf
                        <input type="hidden" name="lat" id="formLat">
                        <input type="hidden" name="lng" id="formLng">
                        <input type="hidden" name="address" id="formAddress">
                        <input type="hidden" name="photo" id="formPhoto">
                        <div>
                            <label class="form-label small fw-semibold text-secondary mb-1">Catatan Kerja (Opsional)</label>
                            <textarea name="notes" id="formNotes" class="form-control form-control-sm" rows="2" placeholder="Tuliskan catatan kerja jika ada..."></textarea>
                        </div>
                    </form>
                </div>
                <!-- 3. Footer Modal -->
                <div class="px-4 py-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <button type="button" class="btn btn-outline-secondary btn-sm px-3" onclick="closeAttendanceModal()">Batal</button>
                    <button type="button" id="btnConfirmSubmit" class="btn btn-sm btn-success px-4 py-1.5 fw-semibold text-white shadow-sm" disabled onclick="submitBladeAttendance()">
                        <i class="bi bi-check-lg me-1"></i> Simpan Absensi
                    </button>
                </div>
            </div>
        </div>

        {{-- LIGHTBOX MODAL BLADE --}}
        <div id="bladeLightbox" class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3" style="background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(6px); z-index: 1070;" onclick="closeLightbox(event)">
            <div class="card border-0 bg-dark text-white shadow-2xl overflow-hidden w-100" style="max-width: 650px; border-radius: 16px; max-height: 92vh;">
                <div class="px-3 py-2 bg-black bg-opacity-50 d-flex justify-content-between align-items-center border-bottom border-secondary">
                    <div>
                        <h6 id="lbTitle" class="mb-0 fw-bold fs-6">Foto Absensi</h6>
                        <small id="lbTime" class="text-white-50" style="font-size: 0.75rem;"></small>
                    </div>
                    <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('bladeLightbox').classList.add('d-none')"></button>
                </div>
                <div class="p-2 text-center bg-black d-flex align-items-center justify-content-center overflow-auto" style="max-height: 65vh;">
                    <img id="lbImg" src="" alt="Foto Absensi" class="img-fluid rounded-2" style="max-height: 60vh; object-fit: contain;">
                </div>
                <div class="p-3 bg-dark border-top border-secondary small">
                    <div class="d-flex align-items-start gap-2 mb-2">
                        <i class="bi bi-geo-alt-fill text-danger fs-6 flex-shrink-0 mt-1"></i>
                        <div>
                            <span id="lbAddress" class="d-block text-white fw-semibold"></span>
                            <span id="lbCoords" class="text-white-50" style="font-size: 0.75rem;"></span>
                        </div>
                    </div>
                    <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-50">
                        <a id="lbMapLink" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-outline-info btn-sm py-1 px-3">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Buka Google Maps
                        </a>
                        <button type="button" class="btn btn-secondary btn-sm px-3" onclick="document.getElementById('bladeLightbox').classList.add('d-none')">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    let bladeStream = null;
    let currentBladeMode = 'CHECK-IN';
    let currentBladeLat = null;
    let currentBladeLng = null;
    let currentBladeAddress = '';

    const empInfo = {
        name: "{{ $employee ? addslashes($employee->name) : addslashes($user->name) }}",
        number: "{{ $employee ? addslashes($employee->employee_number) : 'KARYAWAN' }}",
        position: "{{ $employee ? addslashes($employee->position ?: 'Staf') : 'Staf' }}"
    };

    function refreshBladeLocation() {
        const addressEl = document.getElementById('geoAddress');
        const coordsEl = document.getElementById('geoCoords');
        if (!navigator.geolocation) {
            if (addressEl) addressEl.textContent = 'Geolocation tidak didukung browser ini';
            return;
        }
        if (addressEl) addressEl.textContent = 'Mendeteksi lokasi GPS...';
        navigator.geolocation.getCurrentPosition(
            async (pos) => {
                currentBladeLat = pos.coords.latitude;
                currentBladeLng = pos.coords.longitude;
                if (coordsEl) coordsEl.textContent = `GPS: ${currentBladeLat.toFixed(6)}, ${currentBladeLng.toFixed(6)}`;
                try {
                    const res = await fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${currentBladeLat}&lon=${currentBladeLng}&zoom=18&addressdetails=1`, { headers: { 'Accept': 'application/json' } });
                    if (res.ok) {
                        const data = await res.json();
                        currentBladeAddress = data.display_name || `Koordinat: ${currentBladeLat.toFixed(6)}, ${currentBladeLng.toFixed(6)}`;
                    } else {
                        currentBladeAddress = `Koordinat: ${currentBladeLat.toFixed(6)}, ${currentBladeLng.toFixed(6)}`;
                    }
                } catch {
                    currentBladeAddress = `Koordinat: ${currentBladeLat.toFixed(6)}, ${currentBladeLng.toFixed(6)}`;
                }
                if (addressEl) addressEl.textContent = currentBladeAddress;
            },
            () => {
                if (addressEl) addressEl.textContent = 'Izin lokasi tidak diberikan / tidak dapat diambil';
            },
            { enableHighAccuracy: true, timeout: 8000 }
        );
    }

    async function startBladeCamera() {
        try {
            const video = document.getElementById('bladeVideo');
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) return;
            bladeStream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'user', width: { ideal: 1280 }, height: { ideal: 720 } },
                audio: false
            });
            video.srcObject = bladeStream;
            video.play();
        } catch (e) {
            console.warn('Camera access error:', e);
        }
    }

    function stopBladeCamera() {
        if (bladeStream) {
            bladeStream.getTracks().forEach(t => t.stop());
            bladeStream = null;
        }
    }

    function openAttendanceModal(mode) {
        currentBladeMode = mode;
        const modal = document.getElementById('attendanceModal');
        const title = document.getElementById('modalTitle');
        const header = document.getElementById('modalHeader');
        const form = document.getElementById('attendanceActionForm');
        const btnConfirm = document.getElementById('btnConfirmSubmit');

        modal.classList.remove('d-none');
        retakeBladeSnapshot();
        refreshBladeLocation();
        startBladeCamera();

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

    function closeAttendanceModal() {
        stopBladeCamera();
        document.getElementById('attendanceModal').classList.add('d-none');
    }

    function loadWatermarkLogo() {
        return new Promise(resolve => {
            const img = new Image();
            img.crossOrigin = 'anonymous';
            img.onload = () => resolve(img);
            img.onerror = () => resolve(null);
            img.src = '/images/branding/logo-replay-net.png';
        });
    }

    async function renderBladeWatermark(source, isMirror) {
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
        let addrText = currentBladeAddress ? currentBladeAddress.trim() : '';
        if (!addrText) {
            addrText = currentBladeLat !== null ? 'Alamat belum ditemukan' : 'Lokasi belum terdeteksi';
        }

        let gpsText = '';
        if (currentBladeLat !== null && currentBladeLng !== null) {
            gpsText = `GPS: ${currentBladeLat.toFixed(6)}, ${currentBladeLng.toFixed(6)}`;
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
        const logo = await loadWatermarkLogo();
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

    async function takeBladeSnapshot() {
        const video = document.getElementById('bladeVideo');
        const dataUrl = await renderBladeWatermark(video, true);
        applyBladeSnapshotResult(dataUrl);
    }

    function applyBladeSnapshotResult(dataUrl) {
        stopBladeCamera();
        document.getElementById('videoContainer').classList.add('d-none');
        document.getElementById('photoPreviewContainer').classList.remove('d-none');
        document.getElementById('bladeCapturedImg').src = dataUrl;
        document.getElementById('btnSnap').classList.add('d-none');
        document.getElementById('btnRetake').classList.remove('d-none');
        document.getElementById('btnConfirmSubmit').removeAttribute('disabled');
        document.getElementById('formPhoto').value = dataUrl;
    }

    function retakeBladeSnapshot() {
        document.getElementById('videoContainer').classList.remove('d-none');
        document.getElementById('photoPreviewContainer').classList.add('d-none');
        document.getElementById('bladeCapturedImg').src = '';
        document.getElementById('btnSnap').classList.remove('d-none');
        document.getElementById('btnRetake').classList.add('d-none');
        document.getElementById('btnConfirmSubmit').setAttribute('disabled', 'disabled');
        document.getElementById('formPhoto').value = '';
        startBladeCamera();
    }

    function submitBladeAttendance() {
        document.getElementById('formLat').value = currentBladeLat || '';
        document.getElementById('formLng').value = currentBladeLng || '';
        document.getElementById('formAddress').value = currentBladeAddress || '';
        const btn = document.getElementById('btnConfirmSubmit');
        btn.setAttribute('disabled', 'disabled');
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';
        document.getElementById('attendanceActionForm').submit();
    }

    function openLightbox(url, title, time, address, lat, lng) {
        const lb = document.getElementById('bladeLightbox');
        document.getElementById('lbImg').src = url;
        document.getElementById('lbTitle').textContent = title;
        document.getElementById('lbTime').textContent = time;
        document.getElementById('lbAddress').textContent = address;
        const coordsEl = document.getElementById('lbCoords');
        const mapLink = document.getElementById('lbMapLink');
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

    function closeLightbox(e) {
        if (e.target.id === 'bladeLightbox') {
            document.getElementById('bladeLightbox').classList.add('d-none');
        }
    }

    (function () {
        refreshBladeLocation();
    })();
</script>
@endpush
