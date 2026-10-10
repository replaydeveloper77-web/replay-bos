<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Progress Tiket {{ $ticket->ticket_number }} — PT Replay Inti Media</title>
    <link rel="icon" type="image/png" href="{{ asset('images/branding/favicon-replay-net.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <style>
        :root {
            --brand-navy: #0b1d3a;
            --brand-blue: #2563eb;
            --brand-blue-dark: #1d4ed8;
            --brand-green: #059669;
            --phone-frame-bg: #151922;
            --phone-bezel-border: #2e384d;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: linear-gradient(135deg, #091220 0%, #0f1c30 50%, #172a46 100%);
            min-height: 100vh;
            color: #1e293b;
            margin: 0;
            padding: 24px 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Smartphone Mockup Frame */
        .phone-wrapper {
            position: relative;
            width: 100%;
            max-width: 440px;
            margin: auto;
        }

        .phone-device {
            position: relative;
            background: var(--phone-frame-bg);
            border-radius: 48px;
            padding: 12px;
            box-shadow: 
                0 0 0 2px #26334a,
                0 0 0 6px #10141d,
                0 25px 60px -15px rgba(0, 0, 0, 0.7),
                0 0 80px rgba(37, 99, 235, 0.15);
        }

        /* Side buttons simulation */
        .phone-btn-volume-up, .phone-btn-volume-down, .phone-btn-power {
            position: absolute;
            background: #2a3447;
            border-radius: 4px;
        }
        .phone-btn-volume-up { left: -4px; top: 110px; width: 4px; height: 42px; }
        .phone-btn-volume-down { left: -4px; top: 165px; width: 4px; height: 42px; }
        .phone-btn-power { right: -4px; top: 130px; width: 4px; height: 55px; }

        /* Inner Phone Screen */
        .phone-screen {
            background: #f8fafc;
            border-radius: 38px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 840px;
            max-height: calc(100vh - 60px);
            position: relative;
            box-shadow: inset 0 0 4px rgba(0,0,0,0.15);
        }

        /* Status Bar with Dynamic Island */
        .phone-status-bar {
            background: #ffffff;
            padding: 12px 20px 8px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            font-size: 0.78rem;
            font-weight: 700;
            color: #0f172a;
            z-index: 20;
            position: sticky;
            top: 0;
            border-bottom: 1px solid rgba(226, 232, 240, 0.6);
        }

        .dynamic-island {
            width: 96px;
            height: 24px;
            background: #000000;
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 10px;
        }

        .dynamic-island .camera-lens {
            width: 9px;
            height: 9px;
            background: #111d35;
            border-radius: 50%;
            border: 1.5px solid #1f2e4d;
        }

        .dynamic-island .sensor-dot {
            width: 7px;
            height: 7px;
            background: #082f49;
            border-radius: 50%;
        }

        /* Scrollable Screen Body */
        .phone-content {
            flex: 1;
            overflow-y: auto;
            overflow-x: hidden !important;
            padding: 16px;
            scroll-behavior: smooth;
            min-width: 0;
            width: 100%;
            box-sizing: border-box;
        }
        .phone-content::-webkit-scrollbar {
            width: 4px;
        }
        .phone-content::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        /* App Bar */
        .app-header {
            background: #ffffff;
            border-radius: 20px;
            padding: 14px 16px;
            margin-bottom: 14px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.03);
            min-width: 0;
            overflow: hidden;
            box-sizing: border-box;
        }

        .brand-badge {
            font-size: 0.7rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #2563eb;
            background: #eff6ff;
            padding: 3px 8px;
            border-radius: 6px;
        }

        /* Status Banner */
        .status-hero {
            border-radius: 20px;
            padding: 16px;
            margin-bottom: 14px;
            color: #ffffff;
            position: relative;
            overflow: hidden;
            min-width: 0;
            box-sizing: border-box;
        }

        .status-hero.status-progress {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
        }
        .status-hero.status-resolved {
            background: linear-gradient(135deg, #065f46 0%, #10b981 100%);
        }
        .status-hero.status-pending {
            background: linear-gradient(135deg, #854d0e 0%, #eab308 100%);
        }

        .pulse-dot {
            width: 9px;
            height: 9px;
            border-radius: 50%;
            display: inline-block;
            background: #22c55e;
            box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            animation: pulse 1.8s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0.7); }
            70% { box-shadow: 0 0 0 8px rgba(255, 255, 255, 0); }
            100% { box-shadow: 0 0 0 0 rgba(255, 255, 255, 0); }
        }

        /* Timeline Items */
        .timeline-container {
            position: relative;
            padding-left: 28px;
            min-width: 0;
            width: 100%;
            box-sizing: border-box;
            overflow-x: hidden;
        }

        .timeline-container::before {
            content: '';
            position: absolute;
            left: 11px;
            top: 8px;
            bottom: 24px;
            width: 2px;
            background: #e2e8f0;
        }

        .timeline-step {
            position: relative;
            margin-bottom: 20px;
            min-width: 0;
            width: 100%;
            box-sizing: border-box;
        }

        .timeline-step-icon {
            position: absolute;
            left: -28px;
            top: 2px;
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background: #ffffff;
            border: 2px solid #2563eb;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.7rem;
            color: #2563eb;
            z-index: 2;
        }

        .timeline-step.latest .timeline-step-icon {
            background: #2563eb;
            color: #ffffff;
            box-shadow: 0 0 0 4px #dbeafe;
        }

        .timeline-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 12px 14px;
            box-shadow: 0 2px 4px rgba(15, 23, 42, 0.02);
            transition: transform 0.15s ease;
            min-width: 0;
            width: 100%;
            overflow: hidden;
            box-sizing: border-box;
            text-align: left;
            overflow-wrap: anywhere !important;
            word-break: break-word !important;
            word-wrap: break-word !important;
        }

        /* Prevent offside text across phone mockup */
        .phone-content,
        .phone-content *,
        .timeline-card,
        .timeline-card * {
            box-sizing: border-box;
            min-width: 0;
        }

        .phone-content p,
        .phone-content div,
        .phone-content span,
        .phone-content strong,
        .phone-content h1,
        .phone-content h2,
        .phone-content h3,
        .phone-content h4,
        .phone-content h5,
        .phone-content h6,
        .timeline-card p,
        .timeline-card div,
        .timeline-card span,
        .text-break {
            overflow-wrap: anywhere !important;
            word-break: break-word !important;
            word-wrap: break-word !important;
        }

        .photo-thumb {
            width: 100%;
            height: 85px;
            object-fit: cover;
            border-radius: 10px;
            cursor: pointer;
            transition: transform 0.2s, opacity 0.2s;
            border: 1px solid #e2e8f0;
        }
        .photo-thumb:hover {
            transform: scale(1.03);
            opacity: 0.95;
        }

        /* Home Indicator at Bottom */
        .phone-home-indicator {
            background: #ffffff;
            padding: 10px 0 8px 0;
            display: flex;
            justify-content: center;
            border-top: 1px solid #f1f5f9;
        }
        .home-bar {
            width: 115px;
            height: 4.5px;
            background: #94a3b8;
            border-radius: 6px;
        }

        /* Mobile full-bleed override */
        @media (max-width: 576px) {
            body {
                padding: 0;
                background: #f8fafc;
            }
            .phone-wrapper {
                max-width: 100%;
            }
            .phone-device {
                padding: 0;
                border-radius: 0;
                background: #f8fafc;
                box-shadow: none;
            }
            .phone-btn-volume-up, .phone-btn-volume-down, .phone-btn-power {
                display: none;
            }
            .phone-screen {
                border-radius: 0;
                height: 100vh;
                max-height: 100vh;
            }
        }
    </style>
</head>
<body>

    <div class="phone-wrapper">
        <!-- Physical button simulations on chassis -->
        <div class="phone-btn-volume-up"></div>
        <div class="phone-btn-volume-down"></div>
        <div class="phone-btn-power"></div>

        <!-- Phone Device Frame -->
        <div class="phone-device">
            <div class="phone-screen">

                <!-- Status Bar with Clock & Dynamic Island -->
                <div class="phone-status-bar">
                    <span id="phone-clock">10:15</span>
                    <div class="dynamic-island">
                        <div class="camera-lens"></div>
                        <div class="sensor-dot"></div>
                    </div>
                    <div class="d-flex align-items-center gap-1.5">
                        <i class="bi bi-reception-4" style="font-size: 0.8rem;"></i>
                        <i class="bi bi-wifi" style="font-size: 0.8rem;"></i>
                        <i class="bi bi-battery-full" style="font-size: 0.95rem;"></i>
                    </div>
                </div>

                <!-- Scrollable Phone App Content -->
                <div class="phone-content">

                    <!-- App Brand & Ticket Header -->
                    <div class="app-header">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="brand-badge">REPLAY NOC</span>
                                <span class="text-muted" style="font-size: 0.72rem;">Live Tracking</span>
                            </div>
                            <span class="badge bg-light text-dark border font-monospace" style="font-size: 0.72rem;" id="ticket-number-label">
                                {{ $ticket->ticket_number }}
                            </span>
                        </div>
                        <h6 class="mb-1 fw-bold text-dark" style="font-size: 0.95rem;">{{ $ticket->title }}</h6>
                        <div class="text-muted small" style="font-size: 0.75rem;">
                            <i class="bi bi-clock me-1"></i> Dilaporkan: {{ $ticket->created_at->format('d/m/Y H:i') }} WIB
                        </div>
                    </div>

                    <!-- Status Hero Banner -->
                    @php
                        $isResolved = in_array($ticket->status, ['RESOLVED', 'CLOSED']);
                        $isHandling = in_array($ticket->status, ['HANDLE', 'INVESTIGATING', 'MONITORING', 'WAITING', 'ESCALATED']);
                        
                        $heroClass = $isResolved ? 'status-resolved' : ($isHandling ? 'status-progress' : 'status-pending');
                        $statusTitle = $isResolved 
                            ? ($ticket->status === 'CLOSED' ? 'Tiket Selesai & Ditutup' : 'Perbaikan Selesai (Solved)') 
                            : ($isHandling ? 'Sedang Ditangani Teknisi' : 'Laporan Diterima');
                        $statusDesc = match ($ticket->status) {
                            'RESOLVED' => 'Kendala telah berhasil ditangani oleh tim teknisi. Laporan RFO tersedia di bawah.',
                            'CLOSED' => 'Tiket telah ditutup dan dinyatakan tuntas. Laporan RFO tersedia di bawah.',
                            'HANDLE' => 'Tim teknisi sedang melakukan pengerjaan perbaikan di lapangan/jaringan.',
                            'INVESTIGATING' => 'Tim teknisi sedang menginvestigasi sumber gangguan.',
                            'MONITORING' => 'Perbaikan selesai dan saat ini dalam masa pemantauan kualitas jaringan.',
                            'WAITING' => 'Menunggu koordinasi atau akses ke lokasi perangkat.',
                            default => 'Tiket telah terdaftar dan dalam antrean penugasan teknisi.',
                        };
                    @endphp

                    <div class="status-hero {{ $heroClass }}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-uppercase fw-bold" style="font-size: 0.7rem; letter-spacing: 0.05em; opacity: 0.9;">
                                Status Tiket
                            </span>
                            <div class="d-flex align-items-center gap-1.5">
                                @if ($isResolved)
                                    <i class="bi bi-check-circle-fill text-white" style="font-size: 0.85rem;"></i>
                                @else
                                    <span class="pulse-dot"></span>
                                @endif
                                <span class="fw-bold" style="font-size: 0.75rem;">{{ $ticket->status }}</span>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-1" style="font-size: 1.15rem;">{{ $statusTitle }}</h5>
                        <p class="mb-0 small" style="font-size: 0.8rem; opacity: 0.92;">{{ $statusDesc }}</p>
                    </div>

                    <!-- Client & Service Information Card -->
                    <div class="card bg-white border-0 shadow-xs rounded-4 p-3 mb-3" style="border: 1px solid #e2e8f0 !important;">
                        <div class="fw-bold text-dark small mb-2 d-flex align-items-center gap-1">
                            <i class="bi bi-info-circle text-primary"></i> Detail Layanan
                        </div>
                        <div class="row g-2 small" style="font-size: 0.78rem;">
                            <div class="col-6">
                                <span class="text-muted d-block">Pelanggan:</span>
                                <strong class="text-dark">{{ $ticket->customer?->name ?? ($ticket->customer_name ?: '-') }}</strong>
                            </div>
                            <div class="col-6">
                                <span class="text-muted d-block">Layanan / Paket:</span>
                                <strong class="text-dark">{{ $ticket->customer?->service ?? ($ticket->category ?: '-') }}</strong>
                            </div>
                            @if ($ticket->customer?->service_number)
                                <div class="col-6">
                                    <span class="text-muted d-block">No. Layanan:</span>
                                    <strong class="font-monospace text-primary">{{ $ticket->customer->service_number }}</strong>
                                </div>
                            @endif
                            <div class="col-6">
                                <span class="text-muted d-block">Teknisi / Tim:</span>
                                <strong class="text-dark">{{ $ticket->pic?->name ?? ($ticket->team?->name ?? 'Tim NOC Replay') }}</strong>
                            </div>
                            @if ($ticket->customer?->location)
                                <div class="col-12 mt-2 pt-2 border-top">
                                    <span class="text-muted d-block">Lokasi Pemasangan:</span>
                                    <span class="text-dark"><i class="bi bi-geo-alt text-danger me-1"></i>{{ $ticket->customer->location }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Dedicated RFO (Reason For Outage) & Resolution Card (For Solved / Closed Tickets) -->
                    @if ($isResolved || $ticket->resolution_note)
                        <div class="card bg-white border-0 shadow-sm rounded-4 p-3 mb-3" style="border: 1px solid #86efac !important; background: linear-gradient(180deg, #f0fdf4 0%, #ffffff 100%) !important;">
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-success-subtle">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success text-white" style="width: 26px; height: 26px; font-size: 0.85rem;">
                                        <i class="bi bi-file-earmark-check-fill"></i>
                                    </span>
                                    <div>
                                        <div class="fw-bold text-success" style="font-size: 0.9rem; line-height: 1.2;">Laporan RFO (Reason For Outage)</div>
                                        <div class="text-muted" style="font-size: 0.68rem;">Laporan Hasil Akhir &amp; Penyebab Gangguan</div>
                                    </div>
                                </div>
                                <span class="badge bg-success" style="font-size: 0.7rem;">
                                    <i class="bi bi-check-circle me-0.5"></i> {{ $ticket->status === 'CLOSED' ? 'CLOSED' : 'SOLVED' }}
                                </span>
                            </div>

                            <!-- RFO Content Box -->
                            <div class="p-2.5 rounded-3 mb-2.5" style="background: rgba(240, 253, 244, 0.9); border: 1px dashed #86efac; overflow: hidden;">
                                <div class="text-success-emphasis fw-bold mb-1 d-flex align-items-center gap-1" style="font-size: 0.74rem;">
                                    <i class="bi bi-journal-text text-success"></i> Catatan Hasil Penanganan &amp; Penyebab Gangguan:
                                </div>
                                <div class="text-dark text-break text-start" style="font-size: 0.83rem; line-height: 1.5; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; text-align: left;">{{ trim($ticket->resolution_note) ?: 'Kendala gangguan telah berhasil diselesaikan oleh tim teknisi. Jaringan telah dipulihkan dan dapat digunakan kembali secara normal.' }}</div>
                            </div>

                            <!-- Metrics Grid (Downtime & Resolution Info) -->
                            <div class="row g-2 mb-2" style="font-size: 0.73rem;">
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-light border border-slate-200">
                                        <span class="text-muted d-block" style="font-size: 0.67rem;"><i class="bi bi-stopwatch text-primary me-1"></i>Durasi Gangguan:</span>
                                        <strong class="text-dark">{{ $ticket->outageDuration() ?? '-' }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-light border border-slate-200">
                                        <span class="text-muted d-block" style="font-size: 0.67rem;"><i class="bi bi-calendar-check text-success me-1"></i>Waktu Selesai:</span>
                                        <strong class="text-dark">
                                            @php $resolvedTime = $ticket->resolved_at ?? $ticket->closed_at; @endphp
                                            {{ $resolvedTime ? $resolvedTime->format('d/m/Y H:i') . ' WIB' : '-' }}
                                        </strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-light border border-slate-200">
                                        <span class="text-muted d-block" style="font-size: 0.67rem;"><i class="bi bi-person-badge text-info me-1"></i>Teknisi / Tim:</span>
                                        <strong class="text-dark text-truncate d-block">{{ $ticket->pic?->name ?? ($ticket->team?->name ?? 'Tim NOC') }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-2 rounded-3 bg-light border border-slate-200">
                                        <span class="text-muted d-block" style="font-size: 0.67rem;"><i class="bi bi-broadcast text-success me-1"></i>Status Jaringan:</span>
                                        <strong class="text-success"><i class="bi bi-check-circle-fill me-0.5"></i> Pulih (Normal UP)</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="p-2 rounded-3 text-center" style="background: #f8fafc; border: 1px solid #e2e8f0; font-size: 0.7rem; color: #64748b;">
                                <i class="bi bi-infinity text-success me-1"></i>
                                Link tracking ini <strong>permanen &amp; tidak kadaluarsa</strong>. Anda dapat menyimpan tautan ini sebagai arsip bukti penanganan (RFO).
                            </div>
                        </div>
                    @endif

                    <!-- Timeline Progress Perbaikan -->
                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                        <span class="fw-bold text-dark small">
                            <i class="bi bi-clock-history text-primary me-1"></i> Progres Update Perbaikan
                        </span>
                        <span class="text-muted" style="font-size: 0.7rem;">Real-time Update</span>
                    </div>

                    @php
                        // Collect timeline items sorted descending so latest is at the top for client readability
                        $timelineSorted = collect($timeline)->sortByDesc('at')->values();
                    @endphp

                    <div class="timeline-container mb-3">
                        @forelse ($timelineSorted as $idx => $event)
                            <div class="timeline-step {{ $idx === 0 ? 'latest' : '' }}">
                                <div class="timeline-step-icon">
                                    @if ($event['type'] === 'PROGRESS_UPDATE')
                                        <i class="bi bi-tools"></i>
                                    @elseif ($event['type'] === 'STATUS_CHANGE')
                                        @if ($event['to_status'] === 'RESOLVED')
                                            <i class="bi bi-check-lg"></i>
                                        @else
                                            <i class="bi bi-arrow-repeat"></i>
                                        @endif
                                    @else
                                        <i class="bi bi-person-check"></i>
                                    @endif
                                </div>

                                <div class="timeline-card">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <span class="text-muted" style="font-size: 0.7rem;">
                                            <i class="bi bi-calendar-event me-1"></i>{{ $event['at']->format('d M, H:i') }} WIB
                                        </span>
                                        @if ($event['type'] === 'PROGRESS_UPDATE')
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle" style="font-size: 0.65rem;">
                                                Update Teknisi
                                            </span>
                                        @elseif ($event['type'] === 'STATUS_CHANGE')
                                            <span class="badge bg-slate-100 text-dark border" style="font-size: 0.65rem;">
                                                {{ $event['to_status'] }}
                                            </span>
                                        @endif
                                    </div>

                                    @if ($event['type'] === 'PROGRESS_UPDATE')
                                        @if (!empty($event['title']))
                                            <div class="fw-bold text-dark mb-1 text-break" style="font-size: 0.85rem; overflow-wrap: anywhere; word-break: break-word;">{{ $event['title'] }}</div>
                                        @endif
                                        <div class="text-dark small text-break text-start" style="font-size: 0.8rem; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; line-height: 1.45; text-align: left;">{{ trim($event['note']) }}</div>
                                        <div class="text-muted mt-1 text-break" style="font-size: 0.68rem; overflow-wrap: anywhere;">
                                            Oleh: <strong>{{ $event['user']?->name ?? ($event['user']?->username ?? 'Tim Teknisi') }}</strong>
                                        </div>

                                        <!-- Attached Photos to this Progress Update -->
                                        @if (!empty($event['attachments']) && $event['attachments']->isNotEmpty())
                                            <div class="mt-2 pt-2 border-top">
                                                <div class="text-muted fw-semibold mb-1.5" style="font-size: 0.68rem;">
                                                    <i class="bi bi-camera me-1"></i> Dokumentasi Foto:
                                                </div>
                                                <div class="row g-1.5">
                                                    @foreach ($event['attachments'] as $photo)
                                                        @if ($photo->isImage())
                                                            <div class="col-4">
                                                                <img src="{{ $photo->url() }}" 
                                                                     class="photo-thumb" 
                                                                     alt="{{ $photo->original_filename }}"
                                                                     onclick="openPhotoModal('{{ $photo->url() }}', '{{ addslashes($event['title'] ?? 'Dokumentasi Perbaikan') }}', '{{ $event['at']->format('d M Y, H:i') }} WIB')">
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                    @elseif ($event['type'] === 'STATUS_CHANGE')
                                        <div class="text-dark small text-break" style="font-size: 0.8rem; overflow-wrap: anywhere; word-break: break-word;">
                                            @if (is_null($event['from_status']))
                                                Tiket dibuat dengan status <strong>{{ $event['to_status'] }}</strong>
                                            @elseif ($event['from_status'] === $event['to_status'])
                                                Pembaruan informasi tiket
                                            @else
                                                @if (in_array($event['to_status'], ['RESOLVED', 'CLOSED']))
                                                    Status: <span class="badge bg-success">{{ $event['to_status'] }}</span> &mdash; <span class="text-success fw-semibold">Gangguan Teratasi</span>
                                                @else
                                                    Status berubah: <span class="badge bg-secondary-subtle text-secondary">{{ $event['from_status'] }}</span> &rarr; <span class="badge bg-primary">{{ $event['to_status'] }}</span>
                                                @endif
                                            @endif
                                        </div>
                                        @if ($event['note'])
                                            @if (in_array($event['to_status'], ['RESOLVED', 'CLOSED']))
                                                <div class="mt-2 p-2.5 rounded-3 bg-success-subtle text-success-emphasis border border-success-subtle text-break text-start" style="font-size: 0.74rem; text-align: left;">
                                                    <div class="fw-bold d-flex align-items-center gap-1 mb-1 text-success text-start" style="line-height: 1.2;">
                                                        <i class="bi bi-file-earmark-check"></i> RFO (Catatan Penyelesaian):
                                                    </div>
                                                    <div class="text-dark text-start" style="white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; line-height: 1.45; text-align: left;">{{ trim($event['note']) }}</div>
                                                </div>
                                            @else
                                                <div class="text-muted small mt-1 text-break text-start" style="font-size: 0.74rem; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; line-height: 1.4; text-align: left;">{{ trim($event['note']) }}</div>
                                            @endif
                                        @endif

                                    @else
                                        <div class="text-dark small text-break text-start" style="font-size: 0.8rem; overflow-wrap: anywhere; word-break: break-word; text-align: left;">
                                            Penugasan ke: <strong>{{ $event['assigned_team']?->name ?? ($event['assigned_to']?->name ?? $event['assigned_to']?->username) }}</strong>
                                        </div>
                                        @if ($event['note'])
                                            <div class="text-muted small mt-1 text-break text-start" style="font-size: 0.74rem; white-space: pre-wrap; overflow-wrap: anywhere; word-break: break-word; line-height: 1.4; text-align: left;">{{ trim($event['note']) }}</div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted small">
                                <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                Belum ada riwayat perbaikan yang tercatat.
                            </div>
                        @endforelse
                    </div>

                    <!-- Additional General Gallery (if there are standalone attachments) -->
                    @php
                        $standalonePhotos = $ticket->attachments->whereNull('ticket_progress_update_id')->filter->isImage();
                    @endphp
                    @if ($standalonePhotos->isNotEmpty())
                        <div class="card bg-white border-0 shadow-xs rounded-4 p-3 mb-3" style="border: 1px solid #e2e8f0 !important;">
                            <div class="fw-bold text-dark small mb-2 d-flex align-items-center gap-1">
                                <i class="bi bi-images text-primary"></i> Lampiran Foto Tambahan
                            </div>
                            <div class="row g-2">
                                @foreach ($standalonePhotos as $photo)
                                    <div class="col-4">
                                        <img src="{{ $photo->url() }}" 
                                             class="photo-thumb" 
                                             alt="{{ $photo->original_filename }}"
                                             onclick="openPhotoModal('{{ $photo->url() }}', 'Lampiran Tiket', '{{ $photo->created_at->format('d M Y, H:i') }} WIB')">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- WhatsApp / Help Call-to-action Button -->
                    <div class="card bg-light border-0 rounded-4 p-3 text-center mb-3">
                        <div class="small fw-semibold text-dark mb-1">Ada pertanyaan mengenai perbaikan ini?</div>
                        <p class="text-muted mb-2" style="font-size: 0.72rem;">Hubungi Customer Care &amp; NOC Replay untuk informasi lebih lanjut.</p>
                        @php
                            $waHelpText = rawurlencode("Halo NOC Replay, saya ingin menanyakan perkembangan perbaikan tiket no {$ticket->ticket_number} ({$ticket->title}).");
                        @endphp
                        <a href="https://wa.me/6289684550333?text={{ $waHelpText }}" target="_blank" class="btn btn-success btn-sm rounded-pill fw-semibold py-2">
                            <i class="bi bi-whatsapp me-1"></i> Chat Tim NOC WhatsApp
                        </a>
                    </div>

                    <!-- Footer Notice -->
                    <div class="text-center text-muted pb-3" style="font-size: 0.7rem;">
                        <div>PT Replay Inti Media &copy; {{ date('Y') }}</div>
                        <div class="mt-0.5"><i class="bi bi-infinity text-success me-1"></i>Tautan pantauan progres &amp; arsip RFO resmi &bull; PT Replay Inti Media</div>
                    </div>

                </div>

                <!-- Bottom Home Indicator -->
                <div class="phone-home-indicator">
                    <div class="home-bar"></div>
                </div>

            </div>
        </div>
    </div>

    <!-- Image Lightbox Modal -->
    <div class="modal fade" id="photoPreviewModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content bg-dark border-0 text-white rounded-4 overflow-hidden">
                <div class="modal-header border-0 pb-0">
                    <div>
                        <h6 class="modal-title fw-bold" id="modalPhotoTitle">Dokumentasi</h6>
                        <span class="small text-white-50" id="modalPhotoTime"></span>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center p-3">
                    <img id="modalPhotoImage" src="" class="img-fluid rounded-3 shadow" style="max-height: 75vh; object-fit: contain;" alt="Preview Foto">
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Update live phone clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const clockEl = document.getElementById('phone-clock');
            if (clockEl) {
                clockEl.textContent = `${hours}:${minutes}`;
            }
        }
        updateClock();
        setInterval(updateClock, 10000);

        // Photo Lightbox modal
        const photoModal = new bootstrap.Modal(document.getElementById('photoPreviewModal'));
        function openPhotoModal(url, title, time) {
            document.getElementById('modalPhotoImage').src = url;
            document.getElementById('modalPhotoTitle').textContent = title || 'Dokumentasi Perbaikan';
            document.getElementById('modalPhotoTime').textContent = time || '';
            photoModal.show();
        }
    </script>
</body>
</html>
