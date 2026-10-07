@extends('layouts.app')

@section('title', $ticket->ticket_number)

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h5 class="mb-1">{{ $ticket->ticket_number }} — {{ $ticket->title }}</h5>
            <div>
                @include('tickets._priority_badge')
                @include('tickets._status_badge')
                @include('tickets._sla_badge')
                <span class="badge bg-light text-dark border">{{ \App\Models\Ticket::categoryLabel($ticket->category) }}@if($ticket->subcategory) / {{ $ticket->subcategory }}@endif</span>
            </div>
        </div>
        <div>
            <a href="{{ route('tickets.whatsapp', $ticket) }}" target="_blank" class="btn btn-sm btn-success">
                <i class="bi bi-whatsapp"></i> Kirim ke WhatsApp
            </a>
            @can('update', $ticket)
                <a href="{{ route('tickets.edit', $ticket) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
            @endcan
            @can('delete', $ticket)
                <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus tiket {{ $ticket->ticket_number }}? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                        <i class="bi bi-trash text-danger"></i> Hapus
                    </button>
                </form>
            @endcan
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if (session('wa_error'))
        <div class="alert alert-danger">{{ session('wa_error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-7">
            <div class="noc-stat-card bg-white p-3 mb-3">
                <h6>Deskripsi</h6>
                <p class="mb-3">{{ $ticket->description }}</p>
                <div class="row small text-muted">
                    <div class="col-6 mb-2"><strong>Device:</strong> {{ $ticket->device ?? '-' }}</div>
                    <div class="col-6 mb-2"><strong>Location:</strong> {{ $ticket->location ?? '-' }}</div>
                    <div class="col-6 mb-2"><strong>Service:</strong> {{ $ticket->service ?? '-' }}</div>
                    <div class="col-6 mb-2">
                        <strong>Customer/Pelanggan:</strong>
                        {{ $ticket->customer_name ?? '-' }}
                        @if ($ticket->customer_id)
                            @can('view', \App\Models\Customer::class)
                                (<a href="{{ route('customers.show', $ticket->customer_id) }}" target="_blank">lihat data pelanggan</a>)
                            @endcan
                        @endif
                    </div>
                    <div class="col-6 mb-2"><strong>No. WhatsApp:</strong> {{ $ticket->customer_phone ?? '-' }}</div>
                    <div class="col-6 mb-2"><strong>Reporter:</strong> {{ $ticket->reporter?->name ?? '-' }}</div>
                    <div class="col-6 mb-2"><strong>PIC:</strong> {{ $ticket->pic?->name ?? 'Belum di-assign' }}</div>
                    <div class="col-6 mb-2"><strong>Tim:</strong> {{ $ticket->team?->name ?? 'Belum di-assign' }}</div>
                    <div class="col-6 mb-2"><strong>Dibuat oleh:</strong> {{ $ticket->creator?->name }}</div>
                    <div class="col-6 mb-2"><strong>SLA Due:</strong> {{ $ticket->sla_due_at?->format('d/m/Y H:i') ?? '-' }}</div>
                    @if ($ticket->escalation_level)
                        <div class="col-6 mb-2"><strong>Level Eskalasi:</strong> <span class="badge bg-danger">{{ str_replace('_', ' ', $ticket->escalation_level) }}</span></div>
                    @endif
                </div>
                @if ($ticket->resolution_note)
                    <div class="alert alert-secondary mt-2 mb-0">
                        <strong>Catatan Penyelesaian:</strong> {{ $ticket->resolution_note }}
                    </div>
                @endif
            </div>

            <div class="noc-stat-card bg-white p-3 mb-3">
                <h6 class="mb-3">Dokumentasi / Lampiran</h6>

                @if ($ticket->isPublicLinkExpired())
                    <div class="alert alert-secondary small py-2 mb-3">
                        <i class="bi bi-link-45deg"></i> Link untuk pelanggan sudah <strong>tidak berlaku</strong> (ticket sudah CLOSED).
                    </div>
                @else
                    <div class="input-group input-group-sm mb-3">
                        <span class="input-group-text"><i class="bi bi-link-45deg"></i></span>
                        <input type="text" id="public-link-text" class="form-control" value="{{ $ticket->publicUrl() }}" readonly onclick="this.select()">
                        <button type="button" id="public-link-copy-btn" class="btn btn-outline-success">Copy</button>
                    </div>
                    <div class="form-text mb-3">Link ini bisa dikirim ke pelanggan untuk lihat dokumentasi foto — otomatis tidak berlaku begitu ticket ditutup (CLOSED).</div>
                @endif

                @if ($ticket->attachments->isEmpty())
                    <p class="text-muted small mb-3">Belum ada foto/dokumen yang diupload.</p>
                @else
                    <div class="row g-2 mb-3">
                        @foreach ($ticket->attachments as $attachment)
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="border rounded p-1 text-center position-relative">
                                    @if ($attachment->isImage())
                                        <a href="{{ $attachment->url() }}" target="_blank">
                                            <img src="{{ $attachment->url() }}" class="img-fluid rounded" style="height:90px; object-fit:cover; width:100%;" alt="{{ $attachment->original_filename }}">
                                        </a>
                                    @else
                                        <a href="{{ $attachment->url() }}" target="_blank" class="d-flex flex-column align-items-center justify-content-center text-decoration-none" style="height:90px;">
                                            <i class="bi bi-file-earmark-pdf fs-1 text-danger"></i>
                                        </a>
                                    @endif
                                    <div class="small text-truncate mt-1" title="{{ $attachment->original_filename }}">{{ $attachment->original_filename }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $attachment->uploadedBy?->username }}</div>
                                    @can('update', $ticket)
                                        <form method="POST" action="{{ route('tickets.attachments.destroy', [$ticket, $attachment]) }}" onsubmit="return confirm('Hapus lampiran ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger position-absolute top-0 end-0 py-0 px-1" style="font-size: 0.7rem;">&times;</button>
                                        </form>
                                    @endcan
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @can('update', $ticket)
                    <form method="POST" action="{{ route('tickets.attachments.store', $ticket) }}" enctype="multipart/form-data" class="d-flex gap-2">
                        @csrf
                        <input type="file" name="files[]" class="form-control form-control-sm" multiple accept="image/*,.pdf" required>
                        <button type="submit" class="btn btn-sm btn-primary text-nowrap">Upload</button>
                    </form>
                    <div class="form-text">Foto (jpg/png/gif/webp) atau PDF, maks 10MB per file, sampai 5 file sekaligus.</div>
                @endcan
            </div>

            @if ($ticket->device && $deviceHistory->isNotEmpty())
                <div class="noc-stat-card bg-white p-3 mb-3">
                    <h6 class="mb-3">Riwayat Perbaikan Device Ini <span class="text-muted small fw-normal">({{ $ticket->device }})</span></h6>
                    <div class="table-responsive">
                        <table class="table table-sm align-middle mb-0">
                            <thead>
                                <tr>
                                    <th>Ticket</th>
                                    <th>Judul</th>
                                    <th>Status</th>
                                    <th>Tanggal</th>
                                    <th>Hasil Penanganan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($deviceHistory as $pastTicket)
                                    <tr>
                                        <td><a href="{{ route('tickets.show', $pastTicket) }}">{{ $pastTicket->ticket_number }}</a></td>
                                        <td>{{ $pastTicket->title }}</td>
                                        <td>@include('tickets._status_badge', ['ticket' => $pastTicket])</td>
                                        <td>{{ $pastTicket->created_at->format('d/m/Y') }}</td>
                                        <td class="small text-muted">{{ \Illuminate\Support\Str::limit($pastTicket->resolution_note, 60) ?: '-' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            <div class="noc-stat-card bg-white p-3">
                <h6 class="mb-3">Timeline</h6>
                <ul class="list-unstyled mb-0">
                    @forelse ($timeline as $event)
                        <li class="d-flex mb-3">
                            <div class="me-3 text-muted small" style="width: 130px;">{{ $event['at']->format('d/m H:i:s') }}</div>
                            <div>
                                @if ($event['type'] === 'STATUS_CHANGE')
                                    <div>
                                        <strong>{{ $event['user']?->username }}</strong>
                                        @if (is_null($event['from_status']))
                                            membuat ticket dengan status <span class="badge bg-light text-dark border">{{ $event['to_status'] }}</span>
                                        @elseif ($event['from_status'] === $event['to_status'])
                                            mengedit detail ticket
                                        @else
                                            mengubah status <span class="badge bg-light text-dark border">{{ $event['from_status'] }}</span> &rarr; <span class="badge bg-light text-dark border">{{ $event['to_status'] }}</span>
                                        @endif
                                    </div>
                                @else
                                    <div>
                                        <strong>{{ $event['user']?->username }}</strong>
                                        @if ($event['assigned_team'])
                                            meng-assign ke tim <strong>{{ $event['assigned_team']->name }}</strong>
                                        @else
                                            meng-assign ke <strong>{{ $event['assigned_to']?->username }}</strong>
                                        @endif
                                    </div>
                                @endif
                                @if ($event['note'])
                                    <div class="text-muted small">{{ $event['note'] }}</div>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="text-muted">Belum ada aktivitas.</li>
                    @endforelse
                </ul>
            </div>

            <div class="noc-stat-card bg-white p-3 mt-3">
                <h6 class="mb-3">Riwayat Komunikasi Pelanggan</h6>
                @forelse ($ticket->whatsAppMessages as $wa)
                    <div class="border-bottom pb-2 mb-2">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <strong>{{ $wa->recipient_name ?? '(tanpa nama)' }}</strong>
                                <span class="text-muted small">({{ $wa->maskedPhone() }})</span>
                                @if ($wa->template_key)
                                    <span class="badge bg-light text-dark border">{{ $wa->template_key }}</span>
                                @endif
                            </div>
                            <span class="badge {{ $wa->status === 'SENT' ? 'bg-success' : ($wa->status === 'FAILED' ? 'bg-danger' : 'bg-secondary') }}">{{ $wa->status }}</span>
                        </div>
                        <div class="small text-muted">{{ $wa->created_at->format('d/m/Y H:i') }} oleh {{ $wa->sentBy?->username }}</div>
                        @if ($wa->status === 'FAILED' && $wa->error_message)
                            <div class="small text-danger">{{ $wa->error_message }}</div>
                        @endif
                    </div>
                @empty
                    <p class="text-muted small mb-0">Belum ada komunikasi WhatsApp untuk ticket ini.</p>
                @endforelse
            </div>
        </div>

        <div class="col-lg-5">
            <div class="noc-stat-card bg-white p-3 mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="mb-0">Template Pesan WhatsApp</h6>
                    <button type="button" id="wa-copy-btn" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-clipboard"></i> Copy
                    </button>
                </div>
                <textarea id="wa-template-text" class="form-control form-control-sm" rows="8" readonly onclick="this.select()">{{ $whatsappText }}</textarea>
                <div class="form-text">Salin teks di atas untuk ditempel manual ke WhatsApp Web/Desktop, atau pakai tombol "Kirim ke WhatsApp" di atas untuk buka WhatsApp langsung.</div>
            </div>

            @can('whatsapp.send')
                <div class="noc-stat-card bg-white p-3 mb-3">
                    <h6 class="mb-2">Kirim WhatsApp (Template)</h6>
                    <form method="POST" action="{{ route('tickets.whatsapp-messages.store', $ticket) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label small">Kirim ke</label>
                            <select name="recipient_type" id="wa-recipient-type" class="form-select form-select-sm">
                                @if ($ticket->whatsappPhoneNumber())
                                    <option value="CUSTOMER">{{ $ticket->customer_name }} ({{ $ticket->customer_phone }})</option>
                                @endif
                                <option value="CUSTOM">Nomor lain (manual)</option>
                            </select>
                        </div>
                        <div id="wa-custom-fields" class="row" style="{{ $ticket->whatsappPhoneNumber() ? 'display:none;' : '' }}">
                            <div class="col-6 mb-2">
                                <input type="text" name="recipient_name" id="wa-recipient-name" class="form-control form-control-sm" placeholder="Nama penerima">
                            </div>
                            <div class="col-6 mb-2">
                                <input type="text" name="phone" id="wa-recipient-phone" class="form-control form-control-sm" placeholder="08xxxxxxxxxx">
                            </div>
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Template</label>
                            <select id="wa-template-select" name="template_key" class="form-select form-select-sm" onchange="window.renderWaTemplate && window.renderWaTemplate()">
                                <option value="">-- Tanpa template (tulis manual) --</option>
                                @foreach ($whatsappTemplates as $tpl)
                                    <option value="{{ $tpl->key }}" data-body="{{ $tpl->body }}">{{ $tpl->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <textarea name="message" id="wa-send-message" class="form-control form-control-sm mb-2" rows="8" placeholder="Pilih template di atas, atau tulis pesan manual di sini"></textarea>
                        <button type="submit" class="btn btn-sm btn-success w-100">
                            <i class="bi bi-whatsapp"></i> Kirim WhatsApp
                        </button>
                    </form>
                </div>
            @endcan

            @can('assign', $ticket)
                <div class="noc-stat-card bg-white p-3 mb-3">
                    <h6>Assign PIC</h6>
                    <form method="POST" action="{{ route('tickets.assign', $ticket) }}">
                        @csrf
                        <select name="assigned_to_id" class="form-select mb-2" required>
                            <option value="">-- Pilih User --</option>
                            @foreach ($assignableUsers as $user)
                                <option value="{{ $user->id }}" {{ $ticket->pic_id === $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->username }})</option>
                            @endforeach
                        </select>
                        <textarea name="note" class="form-control mb-2" placeholder="Catatan (opsional)" rows="2"></textarea>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Assign</button>
                    </form>
                </div>
            @endcan

            @can('assign', $ticket)
                <div class="noc-stat-card bg-white p-3 mb-3">
                    <h6>Assign ke Tim</h6>
                    <form method="POST" action="{{ route('tickets.assign-team', $ticket) }}">
                        @csrf
                        <select name="team_id" class="form-select mb-2" required>
                            <option value="">-- Pilih Tim --</option>
                            @foreach ($assignableTeams as $team)
                                <option value="{{ $team->id }}" {{ $ticket->team_id === $team->id ? 'selected' : '' }}>{{ $team->name }}</option>
                            @endforeach
                        </select>
                        <textarea name="note" class="form-control mb-2" placeholder="Catatan (opsional)" rows="2"></textarea>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Assign ke Tim</button>
                    </form>
                </div>
            @endcan

            @can('update', $ticket)
                <div class="noc-stat-card bg-white p-3">
                    <h6>Update Status</h6>
                    <form method="POST" action="{{ route('tickets.status', $ticket) }}">
                        @csrf
                        <select name="status" class="form-select mb-2" required>
                            @foreach ($statuses as $status)
                                <option value="{{ $status }}" {{ $ticket->status === $status ? 'selected' : '' }}>{{ $status === 'HANDLE' ? 'HANDLE (Sedang Ditangani)' : $status }}</option>
                            @endforeach
                        </select>
                        <select name="escalation_level" class="form-select mb-2">
                            <option value="">-- Level Eskalasi (isi kalau status ESCALATED) --</option>
                            @foreach ($escalationLevels as $level)
                                <option value="{{ $level }}">{{ str_replace('_', ' ', $level) }}</option>
                            @endforeach
                        </select>
                        <textarea name="note" class="form-control mb-2" placeholder="Catatan (wajib untuk RESOLVED/CLOSED/ESCALATED)" rows="3"></textarea>
                        <button type="submit" class="btn btn-primary btn-sm w-100">Update Status</button>
                    </form>
                </div>
            @endcan
        </div>
    </div>

    @include('tickets._troubleshooting')

    @push('scripts')
    <script>
        function wireCopyButton(btnId, sourceId, defaultLabel) {
            var btn = document.getElementById(btnId);
            var source = document.getElementById(sourceId);
            if (!btn || !source) return;

            btn.addEventListener('click', function () {
                var restore = function () {
                    btn.innerHTML = defaultLabel;
                    btn.classList.remove('btn-success');
                    btn.classList.add('btn-outline-success');
                };
                var showCopied = function () {
                    btn.innerHTML = '<i class="bi bi-check-lg"></i> Tersalin!';
                    btn.classList.remove('btn-outline-success');
                    btn.classList.add('btn-success');
                    setTimeout(restore, 1500);
                };

                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(source.value).then(showCopied);
                } else {
                    source.select();
                    document.execCommand('copy');
                    showCopied();
                }
            });
        }

        wireCopyButton('wa-copy-btn', 'wa-template-text', '<i class="bi bi-clipboard"></i> Copy');
        wireCopyButton('public-link-copy-btn', 'public-link-text', 'Copy');

        (function () {
            var ticketData = {
                nama: @json($ticket->customer_name ?? ''),
                layanan: @json($ticket->service ?: $ticket->category),
                lokasi: @json($ticket->location ?: '-'),
                ticket_id: @json($ticket->ticket_number),
                status: @json($ticket->status),
                waktu_mulai: @json($ticket->created_at->format('d/m/Y H:i')),
                waktu_resolved: @json($ticket->resolved_at?->format('d/m/Y H:i') ?? '-'),
            };

            function renderTemplate() {
                var templateSelect = document.getElementById('wa-template-select');
                var messageBox = document.getElementById('wa-send-message');
                var recipientType = document.getElementById('wa-recipient-type');
                var recipientNameInput = document.getElementById('wa-recipient-name');

                if (!templateSelect || !messageBox) return;

                var opt = templateSelect.options[templateSelect.selectedIndex];
                if (!opt) return;

                var body = opt.getAttribute('data-body') || '';
                if (!body) {
                    if (templateSelect.value === '' || templateSelect.value === 'CUSTOM') {
                        messageBox.value = '';
                    }
                    return;
                }

                var nama = (recipientType && recipientType.value === 'CUSTOM' && recipientNameInput && recipientNameInput.value)
                    ? recipientNameInput.value
                    : ticketData.nama;

                var text = body
                    .split('@{{nama}}').join(nama || '-')
                    .split('@{{layanan}}').join(ticketData.layanan || '-')
                    .split('@{{lokasi}}').join(ticketData.lokasi || '-')
                    .split('@{{ticket_id}}').join(ticketData.ticket_id || '-')
                    .split('@{{status}}').join(ticketData.status || '-')
                    .split('@{{waktu_mulai}}').join(ticketData.waktu_mulai || '-')
                    .split('@{{waktu_resolved}}').join(ticketData.waktu_resolved || '-');

                messageBox.value = text;
            }

            window.renderWaTemplate = renderTemplate;

            var templateSelect = document.getElementById('wa-template-select');
            var recipientType = document.getElementById('wa-recipient-type');
            var customFields = document.getElementById('wa-custom-fields');
            var recipientNameInput = document.getElementById('wa-recipient-name');

            templateSelect?.addEventListener('change', renderTemplate);
            recipientNameInput?.addEventListener('input', renderTemplate);
            recipientType?.addEventListener('change', function () {
                if (customFields) {
                    customFields.style.display = recipientType.value === 'CUSTOM' ? 'block' : 'none';
                }
                renderTemplate();
            });

            document.addEventListener('change', function (e) {
                if (e.target && e.target.id === 'wa-template-select') {
                    renderTemplate();
                }
            });

            if (templateSelect && templateSelect.value) {
                renderTemplate();
            }
        })();
    </script>
    @endpush
@endsection
