@extends('layouts.app')

@section('title', 'Data Karyawan')

@section('content')
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h5 class="mb-0 fw-bold">Data Karyawan</h5>
            <small class="text-muted">Master data & direktori seluruh karyawan PT Replay Inti Media</small>
        </div>
        <div class="d-flex gap-2">
            @if ($canManage)
                <button type="button" class="btn btn-primary btn-sm shadow-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan">
                    <i class="bi bi-person-plus-fill me-1"></i> + Tambah Data Karyawan
                </button>
            @endif
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm" role="alert">
            <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- KOTAK PENCARIAN (Sesuai Desain Mockup) --}}
    <div class="card mb-4 border-0 shadow-sm" style="border-radius: 8px; overflow: hidden;">
        <div class="bg-secondary bg-opacity-25 py-2 px-3 text-center border-bottom">
            <h6 class="mb-0 fw-bold text-dark tracking-wide" style="letter-spacing: 2px;">PENCARIAN</h6>
        </div>
        <div class="card-body p-3 bg-white">
            <form method="GET" action="{{ route('employees.index') }}">
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small text-muted mb-1 fw-semibold">Kata Kunci Pencarian</label>
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0" placeholder="Cari Nama, Jabatan, NIK, Email, No. JMO, No. KPJ...">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label small text-muted mb-1 fw-semibold">Departemen</label>
                        <select name="department" class="form-select form-select-sm">
                            <option value="">-- Semua Departemen --</option>
                            @foreach (\App\Models\Employee::DEPARTMENTS as $dept)
                                <option value="{{ $dept }}" @selected(request('department') === $dept)>{{ ucfirst(strtolower($dept)) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label small text-muted mb-1 fw-semibold">Status</label>
                        <select name="status" class="form-select form-select-sm">
                            <option value="">-- Semua Status --</option>
                            <option value="1" @selected(request('status') === '1')>Aktif</option>
                            <option value="0" @selected(request('status') === '0')>Nonaktif (Resign)</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex gap-1">
                        <button type="submit" class="btn btn-primary btn-sm flex-fill">
                            <i class="bi bi-search"></i> Cari
                        </button>
                        @if (request()->hasAny(['q', 'department', 'status']))
                            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset filter">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- TABEL DATA KARYAWAN (Sesuai Kolom Mockup) --}}
    <div class="card border-0 shadow-sm" style="border-radius: 8px; overflow: hidden;">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" style="border: 1px solid #d1d5db;">
                <thead style="background-color: #e5e7eb; color: #111827; border-bottom: 2px solid #9ca3af;">
                    <tr class="text-uppercase fw-bold text-nowrap" style="font-size: 0.85rem; letter-spacing: 0.5px;">
                        <th style="width: 50px; text-align: center; border-right: 1px solid #d1d5db;">NO.</th>
                        <th style="border-right: 1px solid #d1d5db;">NAMA</th>
                        <th style="border-right: 1px solid #d1d5db;">JABATAN</th>
                        <th style="border-right: 1px solid #d1d5db;">NIK</th>
                        <th style="border-right: 1px solid #d1d5db;">EMAIL</th>
                        <th style="border-right: 1px solid #d1d5db;">NO. JMO</th>
                        <th style="border-right: 1px solid #d1d5db;">NO. KPJ</th>
                        <th style="width: 90px; text-align: center; border-right: 1px solid #d1d5db;">STATUS</th>
                        <th style="width: 140px; text-align: center;">AKSI</th>
                    </tr>
                </thead>
                <tbody style="font-size: 0.9rem;">
                    @forelse ($employees as $index => $item)
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td class="text-center fw-semibold text-muted" style="border-right: 1px solid #e5e7eb;">
                                {{ $employees->firstItem() + $index }}
                            </td>
                            <td style="border-right: 1px solid #e5e7eb;">
                                <div class="d-flex align-items-center gap-2">
                                    <img src="{{ $item->photoUrl() }}" alt="{{ $item->name }}" class="rounded shadow-xs border" style="width: 34px; height: 44px; object-fit: cover; flex-shrink: 0;">
                                    <div>
                                        <a href="{{ route('employees.show', $item) }}" class="fw-bold text-dark text-decoration-none">
                                            {{ $item->name }}
                                        </a>
                                        <div class="small text-muted" style="font-size: 0.75rem;">
                                            <code>{{ $item->employee_number }}</code>
                                            @if($item->phone) • <i class="bi bi-whatsapp text-success"></i> {{ $item->phone }} @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td style="border-right: 1px solid #e5e7eb;">
                                <span class="fw-medium text-dark">{{ $item->position ?: '-' }}</span>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">{{ ucfirst(strtolower($item->department)) }}</small>
                            </td>
                            <td style="border-right: 1px solid #e5e7eb;" class="font-monospace">
                                {{ $item->nik_ktp ?: '-' }}
                            </td>
                            <td style="border-right: 1px solid #e5e7eb;">
                                @if ($item->email)
                                    <a href="mailto:{{ $item->email }}" class="text-decoration-none text-primary">{{ $item->email }}</a>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="border-right: 1px solid #e5e7eb;" class="font-monospace text-secondary">
                                {{ $item->no_jmo ?: '-' }}
                            </td>
                            <td style="border-right: 1px solid #e5e7eb;" class="font-monospace text-secondary">
                                {{ $item->no_kpj ?: '-' }}
                            </td>
                            <td class="text-center" style="border-right: 1px solid #e5e7eb;">
                                @if ($item->is_active)
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Aktif</span>
                                @else
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="{{ route('employees.show', $item) }}" class="btn btn-sm btn-outline-info py-0 px-2" title="Lihat Detail Profil">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if ($canManage)
                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" title="Edit Data Karyawan" data-bs-toggle="modal" data-bs-target="#modalEdit{{ $item->id }}">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        @if ($item->is_active)
                                            <form method="POST" action="{{ route('employees.disable', $item) }}" class="d-inline" onsubmit="return confirm('Tandai karyawan {{ $item->name }} tidak aktif/resign?')">
                                                @csrf
                                                <input type="hidden" name="redirect_to" value="{{ route('employees.index') }}">
                                                <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Tandai Nonaktif">
                                                    <i class="bi bi-person-x"></i>
                                                </button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('employees.enable', $item) }}" class="d-inline">
                                                @csrf
                                                <input type="hidden" name="redirect_to" value="{{ route('employees.index') }}">
                                                <button type="submit" class="btn btn-sm btn-outline-success py-0 px-2" title="Aktifkan Kembali">
                                                    <i class="bi bi-person-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>

                        {{-- MODAL EDIT KARYAWAN --}}
                        @if ($canManage)
                            <div class="modal fade" id="modalEdit{{ $item->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                    <div class="modal-content">
                                        <form method="POST" action="{{ route('employees.update', $item) }}" enctype="multipart/form-data">
                                            @csrf
                                            @method('PUT')
                                            <input type="hidden" name="redirect_to" value="{{ route('employees.index') }}">
                                            <div class="modal-header bg-primary text-white">
                                                <h6 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>Edit Data Karyawan — {{ $item->name }}</h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-4 text-start">
                                                <div class="row g-3">
                                                    {{-- Foto 3x4 & Pratinjau --}}
                                                    <div class="col-12 d-flex align-items-center gap-3 p-3 bg-light rounded border">
                                                        <img src="{{ $item->photoUrl() }}" id="previewFotoEdit{{ $item->id }}" alt="Foto 3x4" class="rounded border shadow-xs" style="width: 60px; height: 80px; object-fit: cover;">
                                                        <div class="flex-grow-1">
                                                            <label class="form-label fw-semibold small mb-1">Ganti Foto 3x4 (Opsional)</label>
                                                            <input type="file" name="photo" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'previewFotoEdit{{ $item->id }}')">
                                                            <div class="form-text" style="font-size: 0.75rem;">Format JPG/PNG/WEBP, rasio foto 3x4 (maks. 5MB).</div>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                                        <input type="text" name="name" value="{{ old('name', $item->name) }}" class="form-control form-control-sm" required>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Jabatan <span class="text-danger">*</span></label>
                                                        <input type="text" name="position" value="{{ old('position', $item->position) }}" class="form-control form-control-sm" placeholder="Contoh: Teknisi Lapangan, HR Specialist, dsb." required>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">NIK (KTP)</label>
                                                        <input type="text" name="nik_ktp" value="{{ old('nik_ktp', $item->nik_ktp) }}" class="form-control form-control-sm" placeholder="16 digit NIK KTP">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Email</label>
                                                        <input type="email" name="email" value="{{ old('email', $item->email) }}" class="form-control form-control-sm" placeholder="nama@replay.net.id">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Nomor JMO (BPJS TK)</label>
                                                        <input type="text" name="no_jmo" value="{{ old('no_jmo', $item->no_jmo) }}" class="form-control form-control-sm" placeholder="Nomor akun / ID JMO">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Nomor KPJ (Jamsostek)</label>
                                                        <input type="text" name="no_kpj" value="{{ old('no_kpj', $item->no_kpj) }}" class="form-control form-control-sm" placeholder="Nomor KPJ">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Nomor HP / WhatsApp</label>
                                                        <input type="text" name="phone" value="{{ old('phone', $item->phone) }}" class="form-control form-control-sm" placeholder="08xxxxxxxxxx">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Departemen</label>
                                                        <select name="department" class="form-select form-select-sm">
                                                            @foreach (\App\Models\Employee::DEPARTMENTS as $dept)
                                                                <option value="{{ $dept }}" @selected(old('department', $item->department) === $dept)>{{ ucfirst(strtolower($dept)) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Tahun / Tanggal Masuk</label>
                                                        <input type="date" name="join_date" value="{{ old('join_date', $item->join_date?->format('Y-m-d')) }}" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Akhir Bekerja (Resign)</label>
                                                        <input type="date" name="resign_date" value="{{ old('resign_date', $item->resign_date?->format('Y-m-d')) }}" class="form-control form-control-sm">
                                                    </div>

                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Status Karyawan <span class="text-danger">*</span></label>
                                                        <select name="is_active" class="form-select form-select-sm">
                                                            <option value="1" @selected(old('is_active', $item->is_active) == true)>Aktif</option>
                                                            <option value="0" @selected(old('is_active', $item->is_active) == false)>Nonaktif (Resign)</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label small fw-semibold">Status Kepegawaian</label>
                                                        <select name="employment_status" class="form-select form-select-sm">
                                                            @foreach (\App\Models\Employee::EMPLOYMENT_STATUSES as $estatus)
                                                                <option value="{{ $estatus }}" @selected(old('employment_status', $item->employment_status) === $estatus)>{{ ucfirst(strtolower($estatus)) }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label small fw-semibold">Alamat Lengkap</label>
                                                        <textarea name="address" rows="2" class="form-control form-control-sm" placeholder="Alamat domisili / tempat tinggal">{{ old('address', $item->address) }}</textarea>
                                                    </div>

                                                    <div class="col-12">
                                                        <label class="form-label small fw-semibold">Akun Login Terkait (Opsional)</label>
                                                        <select name="user_id" class="form-select form-select-sm">
                                                            <option value="">-- Tidak ditautkan ke akun login --</option>
                                                            @foreach ($users as $u)
                                                                <option value="{{ $u->id }}" @selected(old('user_id', $item->user_id) == $u->id)>{{ $u->name }} ({{ $u->username }})</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer py-2 bg-light">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                                <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-save me-1"></i> Simpan Perubahan</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-people text-secondary fs-1 d-block mb-2"></i>
                                Belum ada data karyawan yang ditemukan.
                                @if ($canManage)
                                    <div class="mt-2">
                                        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalTambahKaryawan">
                                            <i class="bi bi-person-plus-fill me-1"></i> Tambah Karyawan Pertama
                                        </button>
                                    </div>
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if ($employees->hasPages())
            <div class="card-footer bg-white border-top-0 py-3">
                {{ $employees->links() }}
            </div>
        @endif
    </div>

    {{-- MODAL TAMBAH DATA KARYAWAN --}}
    @if ($canManage)
        <div class="modal fade" id="modalTambahKaryawan" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="redirect_to" value="{{ route('employees.index') }}">
                        <div class="modal-header bg-primary text-white">
                            <h6 class="modal-title fw-bold"><i class="bi bi-person-plus-fill me-2"></i>Tambah Data Karyawan</h6>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4 text-start">
                            <div class="row g-3">
                                {{-- Upload Foto 3x4 --}}
                                <div class="col-12 d-flex align-items-center gap-3 p-3 bg-light rounded border">
                                    <img src="https://ui-avatars.com/api/?name=Foto+3x4&color=4F46E5&background=EEF2FF&size=128" id="previewFotoTambah" alt="Foto 3x4" class="rounded border shadow-xs" style="width: 60px; height: 80px; object-fit: cover;">
                                    <div class="flex-grow-1">
                                        <label class="form-label fw-semibold small mb-1">Unggah Foto 3x4 (Opsional)</label>
                                        <input type="file" name="photo" class="form-control form-control-sm" accept="image/*" onchange="previewImage(this, 'previewFotoTambah')">
                                        <div class="form-text" style="font-size: 0.75rem;">Format JPG/PNG/WEBP, rasio foto 3x4 (maks. 5MB).</div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                                    <input type="text" name="name" value="{{ old('name') }}" class="form-control form-control-sm" placeholder="Nama lengkap karyawan" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Jabatan <span class="text-danger">*</span></label>
                                    <input type="text" name="position" value="{{ old('position') }}" class="form-control form-control-sm" placeholder="Contoh: Teknisi Lapangan, HR, NOC, dsb." required>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">NIK (KTP)</label>
                                    <input type="text" name="nik_ktp" value="{{ old('nik_ktp') }}" class="form-control form-control-sm" placeholder="16 digit NIK KTP">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Email</label>
                                    <input type="email" name="email" value="{{ old('email') }}" class="form-control form-control-sm" placeholder="nama@replay.net.id">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nomor JMO (BPJS TK)</label>
                                    <input type="text" name="no_jmo" value="{{ old('no_jmo') }}" class="form-control form-control-sm" placeholder="Nomor akun / ID JMO">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nomor KPJ (Jamsostek)</label>
                                    <input type="text" name="no_kpj" value="{{ old('no_kpj') }}" class="form-control form-control-sm" placeholder="Nomor KPJ">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Nomor HP / WhatsApp</label>
                                    <input type="text" name="phone" value="{{ old('phone') }}" class="form-control form-control-sm" placeholder="08xxxxxxxxxx">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Departemen</label>
                                    <select name="department" class="form-select form-select-sm">
                                        @foreach (\App\Models\Employee::DEPARTMENTS as $dept)
                                            <option value="{{ $dept }}" @selected(old('department', 'OTHER') === $dept)>{{ ucfirst(strtolower($dept)) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Tahun / Tanggal Masuk</label>
                                    <input type="date" name="join_date" value="{{ old('join_date', now()->toDateString()) }}" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Akhir Bekerja (Resign)</label>
                                    <input type="date" name="resign_date" value="{{ old('resign_date') }}" class="form-control form-control-sm">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Status Karyawan <span class="text-danger">*</span></label>
                                    <select name="is_active" class="form-select form-select-sm">
                                        <option value="1" @selected(old('is_active', '1') == '1')>Aktif</option>
                                        <option value="0" @selected(old('is_active') == '0')>Nonaktif (Resign)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-semibold">Status Kepegawaian</label>
                                    <select name="employment_status" class="form-select form-select-sm">
                                        @foreach (\App\Models\Employee::EMPLOYMENT_STATUSES as $estatus)
                                            <option value="{{ $estatus }}" @selected(old('employment_status', 'TETAP') === $estatus)>{{ ucfirst(strtolower($estatus)) }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Alamat Lengkap</label>
                                    <textarea name="address" rows="2" class="form-control form-control-sm" placeholder="Alamat domisili / tempat tinggal">{{ old('address') }}</textarea>
                                </div>

                                <div class="col-12">
                                    <label class="form-label small fw-semibold">Tautkan ke Akun Login (Opsional)</label>
                                    <select name="user_id" class="form-select form-select-sm">
                                        <option value="">-- Tidak ditautkan ke akun login --</option>
                                        @foreach ($users as $u)
                                            <option value="{{ $u->id }}" @selected(old('user_id') == $u->id)>{{ $u->name }} ({{ $u->username }})</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer py-2 bg-light">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary btn-sm"><i class="bi bi-check-lg me-1"></i> Simpan Data Karyawan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
@endsection

@push('scripts')
<script>
    function previewImage(input, previewId) {
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(previewId);
                if (img) img.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endpush
