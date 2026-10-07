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
                    {{-- Informasi Kepegawaian & Jamsostek --}}
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
                                <span class="text-muted d-block">Nomor JMO (BPJS TK)</span>
                                <strong class="text-primary fs-6">{{ $employee->no_jmo ?: '-' }}</strong>
                            </div>
                            <div class="col-sm-6">
                                <span class="text-muted d-block">Nomor KPJ (Jamsostek)</span>
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

                    <div class="bg-light p-3 rounded mb-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">Status Check-in:</span>
                            @if ($today && $today->isCheckedIn())
                                <span class="badge bg-success"><i class="bi bi-check2"></i> {{ $today->check_in_at->format('H:i') }} WIB</span>
                            @else
                                <span class="badge bg-secondary">Belum Check-in</span>
                            @endif
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted">Status Check-out:</span>
                            @if ($today && $today->isCheckedOut())
                                <span class="badge bg-danger"><i class="bi bi-box-arrow-right"></i> {{ $today->check_out_at->format('H:i') }} WIB</span>
                            @else
                                <span class="badge bg-secondary">Belum Check-out</span>
                            @endif
                        </div>
                    </div>

                    @if (! $today || ! $today->isCheckedIn())
                        <form method="POST" action="{{ route('attendance.check-in') }}" id="portal-checkin-form">
                            @csrf
                            <input type="hidden" name="lat" id="checkin-lat">
                            <input type="hidden" name="lng" id="checkin-lng">
                            <div class="mb-2">
                                <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Catatan kerja (opsional, misal WFH / Dinas Lapangan)"></textarea>
                            </div>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Check-in Sekarang
                            </button>
                        </form>
                    @elseif (! $today->isCheckedOut())
                        <form method="POST" action="{{ route('attendance.check-out') }}" id="portal-checkout-form">
                            @csrf
                            <input type="hidden" name="lat" id="checkout-lat">
                            <input type="hidden" name="lng" id="checkout-lng">
                            <div class="mb-2">
                                <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Catatan penutup (opsional)"></textarea>
                            </div>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="bi bi-box-arrow-right me-1"></i> Check-out Sekarang
                            </button>
                        </form>
                    @else
                        <div class="alert alert-success small mb-0 text-center">
                            <i class="bi bi-check-circle-fill me-1"></i> Absensi hari ini sudah lengkap. Terima kasih atas kerja keras Anda!
                        </div>
                    @endif

                    @if ($attendanceHistory->isNotEmpty())
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="small fw-semibold text-muted mb-2">Riwayat Absensi 7 Hari Terakhir:</h6>
                            <div class="table-responsive">
                                <table class="table table-sm table-borderless small mb-0">
                                    <tbody>
                                        @foreach ($attendanceHistory as $att)
                                            <tr>
                                                <td class="text-muted">{{ $att->date->format('d/m/Y') }}</td>
                                                <td><span class="text-success"><i class="bi bi-arrow-down-right"></i> {{ $att->check_in_at ? $att->check_in_at->format('H:i') : '-' }}</span></td>
                                                <td><span class="text-danger"><i class="bi bi-arrow-up-right"></i> {{ $att->check_out_at ? $att->check_out_at->format('H:i') : '-' }}</span></td>
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
    @endif
@endsection

@push('scripts')
<script>
    (function () {
        if ('geolocation' in navigator) {
            navigator.geolocation.getCurrentPosition(
                function (pos) {
                    const latInp = document.getElementById('checkin-lat');
                    const lngInp = document.getElementById('checkin-lng');
                    const outLat = document.getElementById('checkout-lat');
                    const outLng = document.getElementById('checkout-lng');
                    if (latInp) latInp.value = pos.coords.latitude;
                    if (lngInp) lngInp.value = pos.coords.longitude;
                    if (outLat) outLat.value = pos.coords.latitude;
                    if (outLng) outLng.value = pos.coords.longitude;
                },
                function (err) {
                    // Lokasi opsional
                },
                { enableHighAccuracy: true, timeout: 5000 }
            );
        }
    })();
</script>
@endpush
