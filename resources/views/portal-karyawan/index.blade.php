@extends('layouts.app')

@section('title', 'Portal Karyawan')

@section('content')
    <h5 class="mb-1">Portal Karyawan</h5>
    <p class="text-muted mb-3">{{ $employee->name }} — {{ $employee->position ?? '-' }} ({{ ucfirst(strtolower($employee->department)) }})</p>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-2">Absensi Hari Ini</h6>
                <p class="text-muted small mb-2">{{ now()->translatedFormat('l, d F Y') }}</p>

                @if ($today && $today->isCheckedIn())
                    <p class="mb-2"><i class="bi bi-box-arrow-in-right text-success"></i> Check-in: <strong>{{ $today->check_in_at->format('H:i') }}</strong></p>
                @else
                    <p class="mb-2 text-muted">Belum check-in hari ini.</p>
                @endif

                @if ($today && $today->isCheckedOut())
                    <p class="mb-3"><i class="bi bi-box-arrow-right text-danger"></i> Check-out: <strong>{{ $today->check_out_at->format('H:i') }}</strong></p>
                @endif

                <div id="attendance-status" class="small text-muted mb-2">Mengambil lokasi perangkat...</div>

                @if (! $today || ! $today->isCheckedIn())
                    <form method="POST" action="{{ route('attendance.check-in') }}" id="checkin-form">
                        @csrf
                        <input type="hidden" name="lat" id="checkin-lat">
                        <input type="hidden" name="lng" id="checkin-lng">
                        <div class="mb-2">
                            <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Catatan (opsional, mis. WFH / kunjungan lapangan)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Check-in Sekarang
                        </button>
                    </form>
                @elseif (! $today->isCheckedOut())
                    <form method="POST" action="{{ route('attendance.check-out') }}" id="checkout-form">
                        @csrf
                        <input type="hidden" name="lat" id="checkout-lat">
                        <input type="hidden" name="lng" id="checkout-lng">
                        <div class="mb-2">
                            <textarea name="notes" class="form-control form-control-sm" rows="2" placeholder="Catatan (opsional)"></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="bi bi-box-arrow-right"></i> Check-out Sekarang
                        </button>
                    </form>
                @else
                    <div class="alert alert-success mb-0">Absensi hari ini sudah lengkap. Sampai jumpa besok!</div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Cuti / Izin</h6>
                    <a href="{{ route('leave-requests.self') }}" class="btn btn-sm btn-outline-primary">Ajukan / Lihat Semua</a>
                </div>

                @if ($employee->employment_status === 'TETAP')
                    <p class="small mb-3">
                        Kuota Cuti Tahunan {{ now()->year }}: <strong>{{ $leaveQuota['quota'] }} hari</strong> —
                        Terpakai: <strong>{{ $leaveQuota['used'] }} hari</strong> —
                        Sisa: <strong class="text-success">{{ $leaveQuota['remaining'] }} hari</strong>
                    </p>
                @endif

                @if ($leaveRequests->isEmpty())
                    <p class="text-muted small mb-0">Belum pernah mengajukan cuti/izin.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Tanggal</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($leaveRequests as $lr)
                                    <tr>
                                        <td>{{ $lr->typeLabel() }}</td>
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

        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Riwayat Absensi 7 Hari Terakhir</h6>
                @if ($attendanceHistory->isEmpty())
                    <p class="text-muted small mb-0">Belum ada riwayat absensi sebelumnya.</p>
                @else
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Check-in</th>
                                    <th>Check-out</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($attendanceHistory as $row)
                                    <tr>
                                        <td>{{ $row->date->translatedFormat('d M Y') }}</td>
                                        <td>{{ $row->check_in_at?->format('H:i') ?? '-' }}</td>
                                        <td>{{ $row->check_out_at?->format('H:i') ?? '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Data Saya</h6>
                <table class="table table-sm table-borderless mb-0">
                    <tr>
                        <th style="width: 160px;">Nomor Induk</th>
                        <td>{{ $employee->employee_number }}</td>
                    </tr>
                    <tr>
                        <th>Jabatan</th>
                        <td>{{ $employee->position ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Departemen</th>
                        <td>{{ ucfirst(strtolower($employee->department)) }}</td>
                    </tr>
                    <tr>
                        <th>Status Kepegawaian</th>
                        <td>{{ ucfirst(strtolower($employee->employment_status)) }}</td>
                    </tr>
                    <tr>
                        <th>Tanggal Masuk</th>
                        <td>{{ $employee->join_date?->format('d/m/Y') ?? '-' }}</td>
                    </tr>
                </table>
                <div class="form-text mt-2">Data tidak sesuai? Hubungi HR untuk pembaruan.</div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const statusEl = document.getElementById('attendance-status');
        if (! statusEl) return;

        const targets = [
            ['checkin-lat', 'checkin-lng'],
            ['checkout-lat', 'checkout-lng'],
        ];

        function fillCoords(lat, lng) {
            targets.forEach(([latId, lngId]) => {
                const latEl = document.getElementById(latId);
                const lngEl = document.getElementById(lngId);
                if (latEl) latEl.value = lat;
                if (lngEl) lngEl.value = lng;
            });
        }

        if (! navigator.geolocation) {
            statusEl.textContent = 'Lokasi tidak didukung perangkat ini — absensi tetap bisa dilakukan tanpa lokasi.';
            return;
        }

        navigator.geolocation.getCurrentPosition(
            (pos) => {
                fillCoords(pos.coords.latitude, pos.coords.longitude);
                statusEl.textContent = 'Lokasi berhasil diambil.';
            },
            () => {
                statusEl.textContent = 'Lokasi tidak diizinkan — absensi tetap bisa dilakukan tanpa lokasi.';
            }
        );
    })();
</script>
@endpush
