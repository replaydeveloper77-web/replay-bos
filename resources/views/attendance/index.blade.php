@extends('layouts.app')

@section('title', 'Laporan Absensi')

@section('content')
    <h5 class="mb-3">Laporan Absensi</h5>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="noc-stat-card bg-white p-3 mb-3">
        <form method="GET" class="row g-2">
            <div class="col-md-4">
                <select name="employee_id" class="form-select">
                    <option value="">- Semua Karyawan -</option>
                    @foreach ($employees as $employee)
                        <option value="{{ $employee->id }}" @selected(request('employee_id') == $employee->id)>{{ $employee->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <input type="date" name="date_from" value="{{ request('date_from') }}" class="form-control" placeholder="Dari tanggal">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" value="{{ request('date_to') }}" class="form-control" placeholder="Sampai tanggal">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-outline-primary w-100">Filter</button>
            </div>
        </form>
    </div>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width: 105px;">Tanggal</th>
                        <th style="min-width: 160px;">Karyawan</th>
                        <th style="min-width: 220px;">Check-in (Foto &amp; Lokasi)</th>
                        <th style="min-width: 220px;">Check-out (Foto &amp; Lokasi)</th>
                        <th>Catatan</th>
                        <th style="width: 70px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($attendances as $row)
                        <tr>
                            <td class="text-nowrap fw-semibold">{{ $row->date->format('d-m-Y') }}</td>
                            <td>
                                <strong class="d-block">{{ $row->employee->name }}</strong>
                                <small class="text-muted">{{ $row->employee->employee_number }}</small>
                            </td>
                            <td>
                                <div class="d-flex align-items-start gap-2">
                                    @if ($row->checkInPhotoUrl())
                                        <button type="button" class="btn p-0 border-0 flex-shrink-0" title="Klik untuk melihat foto ber-watermark" onclick="openAdminLightbox('{{ $row->checkInPhotoUrl() }}', 'Check-in: {{ addslashes($row->employee->name) }}', '{{ $row->date->format('d-m-Y') }} • {{ $row->check_in_at?->format('H:i') }} WIB', '{{ addslashes($row->check_in_address ?? '-') }}', '{{ $row->check_in_lat }}', '{{ $row->check_in_lng }}')">
                                            <img src="{{ $row->checkInPhotoUrl() }}" alt="Foto Check-in" class="rounded-3 shadow-xs border border-success" style="width: 44px; height: 44px; object-fit: cover;">
                                        </button>
                                    @endif
                                    <div>
                                        <span class="badge bg-success-subtle text-success border border-success-subtle">
                                            <i class="bi bi-box-arrow-in-right me-1"></i>{{ $row->check_in_at?->format('H:i') ?? '-' }} WIB
                                        </span>
                                        @if ($row->check_in_address)
                                            <div class="small text-muted mt-1" style="font-size: 0.75rem; max-width: 200px; line-height: 1.2;">
                                                <i class="bi bi-geo-alt-fill text-danger"></i> {{ \Illuminate\Support\Str::limit($row->check_in_address, 45) }}
                                            </div>
                                        @endif
                                        @if ($row->check_in_lat && $row->check_in_lng)
                                            <a href="https://www.google.com/maps?q={{ $row->check_in_lat }},{{ $row->check_in_lng }}" target="_blank" rel="noopener noreferrer" class="small text-primary text-decoration-none" style="font-size: 0.7rem;">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>Peta ({{ number_format($row->check_in_lat, 4) }}, {{ number_format($row->check_in_lng, 4) }})
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-start gap-2">
                                    @if ($row->checkOutPhotoUrl())
                                        <button type="button" class="btn p-0 border-0 flex-shrink-0" title="Klik untuk melihat foto ber-watermark" onclick="openAdminLightbox('{{ $row->checkOutPhotoUrl() }}', 'Check-out: {{ addslashes($row->employee->name) }}', '{{ $row->date->format('d-m-Y') }} • {{ $row->check_out_at?->format('H:i') }} WIB', '{{ addslashes($row->check_out_address ?? '-') }}', '{{ $row->check_out_lat }}', '{{ $row->check_out_lng }}')">
                                            <img src="{{ $row->checkOutPhotoUrl() }}" alt="Foto Check-out" class="rounded-3 shadow-xs border border-danger" style="width: 44px; height: 44px; object-fit: cover;">
                                        </button>
                                    @endif
                                    <div>
                                        @if ($row->check_out_at)
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle">
                                                <i class="bi bi-box-arrow-right me-1"></i>{{ $row->check_out_at->format('H:i') }} WIB
                                            </span>
                                            @if ($row->check_out_address)
                                                <div class="small text-muted mt-1" style="font-size: 0.75rem; max-width: 200px; line-height: 1.2;">
                                                    <i class="bi bi-geo-alt-fill text-danger"></i> {{ \Illuminate\Support\Str::limit($row->check_out_address, 45) }}
                                                </div>
                                            @endif
                                            @if ($row->check_out_lat && $row->check_out_lng)
                                                <a href="https://www.google.com/maps?q={{ $row->check_out_lat }},{{ $row->check_out_lng }}" target="_blank" rel="noopener noreferrer" class="small text-primary text-decoration-none" style="font-size: 0.7rem;">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i>Peta ({{ number_format($row->check_out_lat, 4) }}, {{ number_format($row->check_out_lng, 4) }})
                                                </a>
                                            @endif
                                        @else
                                            <span class="badge bg-light text-secondary border">Belum</span>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="small text-muted">{{ \Illuminate\Support\Str::limit($row->notes, 35) ?: '-' }}</td>
                            <td>
                                @can('update', \App\Models\Attendance::class)
                                    <a href="{{ route('attendance.edit', $row) }}" class="btn btn-sm btn-outline-secondary">Koreksi</a>
                                @endcan
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-4">Belum ada data absensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $attendances->withQueryString()->links() }}
    </div>

    {{-- MODAL LIGHTBOX ADMIN --}}
    <div id="adminLightbox" class="d-none position-fixed top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center p-3" style="background: rgba(15, 23, 42, 0.85); backdrop-filter: blur(6px); z-index: 1070;" onclick="if(event.target.id==='adminLightbox') document.getElementById('adminLightbox').classList.add('d-none')">
        <div class="card border-0 bg-dark text-white shadow-2xl overflow-hidden w-100" style="max-width: 650px; border-radius: 16px; max-height: 92vh;">
            <div class="px-3 py-2 bg-black bg-opacity-50 d-flex justify-content-between align-items-center border-bottom border-secondary">
                <div>
                    <h6 id="admTitle" class="mb-0 fw-bold fs-6">Foto Absensi</h6>
                    <small id="admTime" class="text-white-50" style="font-size: 0.75rem;"></small>
                </div>
                <button type="button" class="btn-close btn-close-white" onclick="document.getElementById('adminLightbox').classList.add('d-none')"></button>
            </div>
            <div class="p-2 text-center bg-black d-flex align-items-center justify-content-center overflow-auto" style="max-height: 65vh;">
                <img id="admImg" src="" alt="Foto Absensi" class="img-fluid rounded-2" style="max-height: 60vh; object-fit: contain;">
            </div>
            <div class="p-3 bg-dark border-top border-secondary small">
                <div class="d-flex align-items-start gap-2 mb-2">
                    <i class="bi bi-geo-alt-fill text-danger fs-6 flex-shrink-0 mt-1"></i>
                    <div>
                        <span id="admAddress" class="d-block text-white fw-semibold"></span>
                        <span id="admCoords" class="text-white-50" style="font-size: 0.75rem;"></span>
                    </div>
                </div>
                <div class="d-flex justify-content-between align-items-center pt-2 border-top border-secondary border-opacity-50">
                    <a id="admMapLink" href="#" target="_blank" rel="noopener noreferrer" class="btn btn-outline-info btn-sm py-1 px-3">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka Google Maps
                    </a>
                    <button type="button" class="btn btn-secondary btn-sm px-3" onclick="document.getElementById('adminLightbox').classList.add('d-none')">Tutup</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function openAdminLightbox(url, title, time, address, lat, lng) {
        const lb = document.getElementById('adminLightbox');
        document.getElementById('admImg').src = url;
        document.getElementById('admTitle').textContent = title;
        document.getElementById('admTime').textContent = time;
        document.getElementById('admAddress').textContent = address || '-';
        const coordsEl = document.getElementById('admCoords');
        const mapLink = document.getElementById('admMapLink');
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
</script>
@endpush
