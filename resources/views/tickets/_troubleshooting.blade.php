<div class="noc-stat-card bg-white p-3 mt-3">
    <h6 class="mb-3">Troubleshooting</h6>

    @can('update', $ticket)
        <div class="border rounded p-3 mb-4">
            <h6 class="small text-muted text-uppercase">Mulai Sesi Baru</h6>
            <form method="POST" action="{{ route('troubleshooting.store', $ticket) }}">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">Kategori</label>
                        <select name="category" id="ts-category" class="form-select form-select-sm" required>
                            <option value="">-- Pilih --</option>
                            @foreach ($tsCategories as $category)
                                <option value="{{ $category }}">{{ str_replace('_', ' + ', $category) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-8 mb-2">
                        <label class="form-label small">Observasi Awal</label>
                        <input type="text" name="observation" class="form-control form-control-sm" required placeholder="Apa yang teramati pertama kali?">
                    </div>
                </div>

                <div id="ts-physical-checklist" class="mb-2" style="display:none;">
                    <label class="form-label small">Checklist Fisik</label>
                    <div class="row">
                        @foreach ($physicalChecklist as $item)
                            <div class="col-md-3 col-6">
                                <div class="form-check">
                                    <input type="checkbox" name="physical_checklist[]" value="{{ $item }}" class="form-check-input">
                                    <label class="form-check-label small">{{ $item }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div id="ts-logic-checklist" class="mb-2" style="display:none;">
                    <label class="form-label small">Checklist Logic</label>
                    <div class="row">
                        @foreach ($logicChecklist as $item)
                            <div class="col-md-3 col-6">
                                <div class="form-check">
                                    <input type="checkbox" name="logic_checklist[]" value="{{ $item }}" class="form-check-input">
                                    <label class="form-check-label small">{{ $item }}</label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mb-2">
                    <label class="form-label small">Hipotesis (opsional)</label>
                    <input type="text" name="hypothesis" class="form-control form-control-sm">
                </div>

                <button type="submit" class="btn btn-sm btn-primary">Mulai Sesi</button>
            </form>
        </div>
    @endcan

    @forelse ($ticket->troubleshootingSessions as $session)
        <div class="border rounded p-3 mb-3">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <span class="badge bg-secondary">{{ str_replace('_', ' + ', $session->category) }}</span>
                    <span class="badge {{ $session->status === 'OPEN' ? 'bg-warning text-dark' : 'bg-dark' }}">{{ $session->status }}</span>
                    <span class="text-muted small">oleh {{ $session->createdBy?->username }} — {{ $session->created_at->format('d/m/Y H:i') }}</span>
                </div>
            </div>

            @if ($session->hypothesis)
                <div class="small mb-1"><strong>Hipotesis:</strong> {{ $session->hypothesis }}</div>
            @endif
            @if ($session->physical_checklist)
                <div class="small mb-1"><strong>Checklist Fisik:</strong> {{ implode(', ', $session->physical_checklist) }}</div>
            @endif
            @if ($session->logic_checklist)
                <div class="small mb-1"><strong>Checklist Logic:</strong> {{ implode(', ', $session->logic_checklist) }}</div>
            @endif
            @if ($session->conclusion)
                <div class="alert alert-secondary small mt-2 mb-2"><strong>Kesimpulan:</strong> {{ $session->conclusion }}</div>
            @endif

            <ul class="list-unstyled mt-2 mb-2 ps-2 border-start">
                @foreach ($session->steps as $step)
                    <li class="mb-2 ps-2">
                        <span class="badge bg-light text-dark border">{{ $step->step_type }}</span>
                        <span class="small text-muted">{{ $step->created_at->format('d/m H:i') }} — {{ $step->createdBy?->username }}</span>
                        <div>{{ $step->description }}</div>
                    </li>
                @endforeach
            </ul>

            @can('update', $ticket)
                @if ($session->status === 'OPEN')
                    <div class="row g-2 mt-2">
                        <div class="col-md-7">
                            <form method="POST" action="{{ route('troubleshooting.steps.store', $session) }}" class="d-flex gap-1">
                                @csrf
                                <select name="step_type" class="form-select form-select-sm" style="max-width:160px;" required>
                                    @foreach ($stepTypes as $type)
                                        <option value="{{ $type }}">{{ $type }}</option>
                                    @endforeach
                                </select>
                                <input type="text" name="description" class="form-control form-control-sm" placeholder="Deskripsi step" required>
                                <button type="submit" class="btn btn-sm btn-outline-primary">Tambah</button>
                            </form>
                        </div>
                        <div class="col-md-5">
                            <form method="POST" action="{{ route('troubleshooting.close', $session) }}" class="d-flex gap-1">
                                @csrf
                                <input type="text" name="conclusion" class="form-control form-control-sm" placeholder="Kesimpulan penutup sesi" required>
                                <button type="submit" class="btn btn-sm btn-outline-dark">Tutup Sesi</button>
                            </form>
                        </div>
                    </div>
                @endif
            @endcan
        </div>
    @empty
        <p class="text-muted small mb-0">Belum ada sesi troubleshooting.</p>
    @endforelse
</div>

@push('scripts')
<script>
    document.getElementById('ts-category')?.addEventListener('change', function () {
        var val = this.value;
        var physical = document.getElementById('ts-physical-checklist');
        var logic = document.getElementById('ts-logic-checklist');
        physical.style.display = (val === 'PHYSICAL' || val === 'PHYSICAL_LOGIC') ? 'block' : 'none';
        logic.style.display = (val === 'LOGIC' || val === 'PHYSICAL_LOGIC') ? 'block' : 'none';
    });
</script>
@endpush
