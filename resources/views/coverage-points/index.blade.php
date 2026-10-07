@extends('layouts.app')

@section('title', 'Titik Coverage')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Titik Coverage (POP/Tower)</h5>
        @can('create', \App\Models\CoveragePoint::class)
            <a href="{{ route('coverage-points.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Titik
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="noc-stat-card bg-white p-3 mb-3">
        <div id="overview-map" style="height: 400px; border-radius: .5rem;"></div>
    </div>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Koordinat</th>
                        <th>Radius</th>
                        <th>Catatan</th>
                        <th>Status</th>
                        <th style="width: 130px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($points as $point)
                        <tr>
                            <td>{{ $point->name }}</td>
                            <td class="small"><code>{{ $point->latitude }}, {{ $point->longitude }}</code></td>
                            <td>{{ $point->radius_km }} km</td>
                            <td>{{ $point->notes ?? '-' }}</td>
                            <td>
                                @if ($point->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-grid gap-1">
                                    <a href="{{ route('coverage-points.edit', $point) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                    @if ($point->is_active)
                                        <form method="POST" action="{{ route('coverage-points.disable', $point) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Nonaktifkan titik ini?')">Nonaktifkan</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('coverage-points.enable', $point) }}">
                                            @csrf
                                            <button class="btn btn-sm btn-outline-success w-100">Aktifkan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-muted text-center py-3">Belum ada titik coverage.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
    @endpush

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
        <script>
            (function () {
                var points = @json($pointsJson);

                var map = L.map('overview-map').setView([-6.9808, 108.4834], 11);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors',
                }).addTo(map);

                var bounds = [];
                points.forEach(function (p) {
                    var color = p.active ? '#2f6fed' : '#adb5bd';
                    L.marker([p.lat, p.lng]).addTo(map).bindPopup(p.name);
                    L.circle([p.lat, p.lng], { radius: p.radius_km * 1000, color: color, fillOpacity: 0.08 }).addTo(map);
                    bounds.push([p.lat, p.lng]);
                });

                if (bounds.length) map.fitBounds(bounds, { padding: [30, 30] });
            })();
        </script>
    @endpush
@endsection
