@extends('layouts.app')

@section('title', 'Absensi Saya')

@section('content')
    <h5 class="mb-3">Absensi Saya — {{ $employee->name }}</h5>

    @if (session('status'))
        <div class="alert alert-info">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-4 mb-3" style="max-width: 480px;">
        <p class="text-muted mb-1">Hari ini, {{ now()->translatedFormat('l, d F Y') }}</p>

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

    <div class="noc-stat-card bg-white p-4">
        <h6 class="mb-3">Riwayat 14 Hari Terakhir</h6>
        @if ($history->isEmpty())
            <p class="text-muted small mb-0">Belum ada riwayat absensi sebelumnya.</p>
        @else
            <div class="table-responsive">
                <table class="table table-sm align-middle">
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
                                <td>{{ $row->check_in_at?->format('H:i') ?? '-' }}</td>
                                <td>{{ $row->check_out_at?->format('H:i') ?? '-' }}</td>
                                <td class="small text-muted">{{ $row->notes ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection

@push('scripts')
<script>
    (function () {
        const statusEl = document.getElementById('attendance-status');
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
