<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $ticket->ticket_number }} — Replay</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <style>
        body { background: #f2f4f7; }
        .noc-header { background: #0b1d3a; color: #fff; }
        .noc-card { border: 1px solid #e3e7ee; border-radius: .6rem; }
    </style>
</head>
<body>
    <div class="noc-header py-3 mb-4">
        <div class="container">
            <strong>REPLAY</strong>
            <div class="small text-white-50">PT Replay Inti Media</div>
        </div>
    </div>

    <div class="container" style="max-width: 720px;">
        <div class="noc-card bg-white p-4 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <h5 class="mb-1">{{ $ticket->ticket_number }}</h5>
                    <div class="text-muted">{{ $ticket->title }}</div>
                </div>
                @php
                    $statusLabel = match ($ticket->status) {
                        'OPEN', 'ACKNOWLEDGED' => 'Diterima, menunggu penanganan',
                        'INVESTIGATING', 'HANDLE', 'WAITING', 'ESCALATED' => 'Sedang ditangani',
                        'MONITORING' => 'Dalam pemantauan',
                        'RESOLVED' => 'Selesai',
                        default => $ticket->status,
                    };
                    $statusClass = match ($ticket->status) {
                        'RESOLVED' => 'bg-success',
                        'MONITORING' => 'bg-info text-dark',
                        default => 'bg-warning text-dark',
                    };
                @endphp
                <span class="badge {{ $statusClass }}">{{ $statusLabel }}</span>
            </div>

            @if ($ticket->resolved_at && $ticket->resolution_note)
                <div class="alert alert-success mt-3 mb-0">
                    <strong>Hasil Penanganan:</strong> {{ $ticket->resolution_note }}
                </div>
            @endif
        </div>

        @php $photos = $ticket->attachments->filter->isImage(); @endphp
        <div class="noc-card bg-white p-4">
            <h6 class="mb-3">Dokumentasi Foto</h6>

            @if ($photos->isEmpty())
                <p class="text-muted small mb-0">Belum ada dokumentasi foto untuk ticket ini.</p>
            @else
                <div class="row g-2">
                    @foreach ($photos as $attachment)
                        <div class="col-6 col-md-4">
                            <a href="{{ $attachment->url() }}" target="_blank">
                                <img src="{{ $attachment->url() }}" class="img-fluid rounded border" alt="Dokumentasi {{ $ticket->ticket_number }}">
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <p class="text-muted small text-center mt-4">
            Halaman ini otomatis tidak berlaku lagi setelah ticket ditutup.<br>
            Ada pertanyaan? Hubungi Tim NOC PT Replay Inti Media.
        </p>
    </div>
</body>
</html>
