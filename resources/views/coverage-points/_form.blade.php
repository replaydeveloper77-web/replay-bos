@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="mb-3">
    <label class="form-label">Nama Titik (POP/Tower)</label>
    <input type="text" name="name" value="{{ old('name', $point?->name) }}" class="form-control" required placeholder="mis. POP Pasawahan">
</div>

<div class="mb-3">
    <label class="form-label">Klik peta untuk taruh pin, lalu geser pin-nya kalau perlu koreksi posisi</label>
    <div id="point-map" style="height: 360px; border-radius: .5rem;"></div>
    <div class="form-text">Cari alamat dulu di Google Maps kalau perlu. Klik peta buat taruh pin pertama kali, setelah itu pin bisa langsung di-drag/geser buat pas-in posisinya.</div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Latitude</label>
        <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $point?->latitude) }}" class="form-control" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Longitude</label>
        <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $point?->longitude) }}" class="form-control" required>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Radius Jangkauan (km)</label>
        <input type="text" id="radius_km" name="radius_km" value="{{ old('radius_km', $point?->radius_km ?? 3) }}" class="form-control" required>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Catatan (opsional)</label>
    <input type="text" name="notes" value="{{ old('notes', $point?->notes) }}" class="form-control">
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.css">
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        (function () {
            var startLat = parseFloat(document.getElementById('latitude').value) || -6.9808;
            var startLng = parseFloat(document.getElementById('longitude').value) || 108.4834;
            var hasInitial = !!document.getElementById('latitude').value;

            var map = L.map('point-map').setView([startLat, startLng], hasInitial ? 14 : 11);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
            }).addTo(map);

            var marker = null;
            var circle = null;

            function setCoords(lat, lng) {
                document.getElementById('latitude').value = lat.toFixed(7);
                document.getElementById('longitude').value = lng.toFixed(7);
                drawRadius(lat, lng);
            }

            function placeMarker(lat, lng) {
                setCoords(lat, lng);

                if (marker) map.removeLayer(marker);
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);

                marker.on('drag', function (e) {
                    // Live-update while dragging so the radius circle follows the pin smoothly.
                    setCoords(e.target.getLatLng().lat, e.target.getLatLng().lng);
                });
            }

            function drawRadius(lat, lng) {
                var radiusKm = parseFloat(document.getElementById('radius_km').value) || 0;
                if (circle) map.removeLayer(circle);
                circle = L.circle([lat, lng], { radius: radiusKm * 1000, color: '#2f6fed', fillOpacity: 0.1 }).addTo(map);
            }

            if (hasInitial) {
                placeMarker(startLat, startLng);
            }

            map.on('click', function (e) {
                placeMarker(e.latlng.lat, e.latlng.lng);
            });

            document.getElementById('radius_km').addEventListener('input', function () {
                var lat = parseFloat(document.getElementById('latitude').value);
                var lng = parseFloat(document.getElementById('longitude').value);
                if (!isNaN(lat) && !isNaN(lng)) drawRadius(lat, lng);
            });
        })();
    </script>
@endpush
