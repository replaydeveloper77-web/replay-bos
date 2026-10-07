@extends('layouts.app')

@section('title', $employee->name)

@section('content')
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div class="d-flex align-items-center gap-3">
            <img src="{{ $employee->photoUrl() }}" alt="{{ $employee->name }}" class="rounded border shadow-sm" style="width: 60px; height: 80px; object-fit: cover;">
            <div>
                <h5 class="mb-1">{{ $employee->name }}</h5>
                <code class="small">{{ $employee->employee_number }}</code>
                @if ($employee->is_active)
                    <span class="badge bg-success ms-1">Aktif</span>
                @else
                    <span class="badge bg-danger ms-1">Resign</span>
                @endif
            </div>
        </div>
        <div>
            @can('view', \App\Models\Employee::class)
                <a href="{{ route('employees.contract-pdf', $employee) }}" class="btn btn-sm btn-outline-dark">
                    <i class="bi bi-file-earmark-pdf"></i> Cetak {{ $employee->employment_status === 'TETAP' ? 'PKWTT' : ($employee->employment_status === 'MAGANG' ? 'Perjanjian Magang' : 'PKWT') }}
                </a>
            @endcan
            @can('update', \App\Models\Employee::class)
                <a href="{{ route('employees.edit', $employee) }}" class="btn btn-sm btn-primary">
                    <i class="bi bi-pencil"></i> Edit
                </a>
            @endcan
            <a href="{{ route('employees.index') }}" class="btn btn-sm btn-outline-secondary">Kembali</a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Data Pribadi</h6>
                <div class="row small">
                    <div class="col-6 mb-3">
                        <div class="text-muted">NIK KTP</div>
                        <div>{{ $employee->nik_ktp ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">No. WhatsApp</div>
                        <div>{{ $employee->phone ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Email</div>
                        <div>{{ $employee->email ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Nomor JMO (BPJS TK)</div>
                        <div>{{ $employee->no_jmo ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Nomor KPJ (Jamsostek)</div>
                        <div>{{ $employee->no_kpj ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Jenis Kelamin</div>
                        <div>{{ $employee->gender === 'L' ? 'Laki-laki' : ($employee->gender === 'P' ? 'Perempuan' : '-') }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Tempat, Tanggal Lahir</div>
                        <div>{{ $employee->birth_place ?? '-' }}@if($employee->birth_date), {{ $employee->birth_date->format('d/m/Y') }}@endif</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Alamat</div>
                        <div>{{ $employee->address ?? '-' }}</div>
                    </div>
                </div>
                @if ($employee->notes)
                    <div class="mt-2">
                        <div class="text-muted small">Catatan</div>
                        <div class="small">{{ $employee->notes }}</div>
                    </div>
                @endif
            </div>
        </div>

        <div class="col-lg-6">
            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Kepegawaian</h6>
                <div class="row small">
                    <div class="col-6 mb-3">
                        <div class="text-muted">Jabatan</div>
                        <div>{{ $employee->position ?? '-' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Departemen</div>
                        <div>{{ ucfirst(strtolower($employee->department)) }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Status Kepegawaian</div>
                        <div>{{ ucfirst(strtolower($employee->employment_status)) }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Tanggal Masuk</div>
                        <div>{{ $employee->join_date?->format('d/m/Y') ?? '-' }}</div>
                    </div>
                    @if ($employee->needsContractEndDate())
                        <div class="col-6 mb-3">
                            <div class="text-muted">Tanggal Berakhir Kontrak/Magang</div>
                            <div>{{ $employee->contract_end_date?->format('d/m/Y') ?? '-' }}</div>
                        </div>
                    @endif
                    <div class="col-6 mb-3">
                        <div class="text-muted">{{ $employee->salaryLabel() }}</div>
                        <div>{{ $employee->salary ? 'Rp '.number_format($employee->salary, 0, ',', '.') : '-' }}</div>
                    </div>
                    @if ($employee->resign_date)
                        <div class="col-6 mb-3">
                            <div class="text-muted">Tanggal Resign</div>
                            <div>{{ $employee->resign_date->format('d/m/Y') }}</div>
                        </div>
                    @endif
                    <div class="col-6 mb-3">
                        <div class="text-muted">Akun Login</div>
                        <div>{{ $employee->user?->username ?? '- (belum ada akun)' }}</div>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="text-muted">Dicatat oleh</div>
                        <div>{{ $employee->createdBy?->username }} — {{ $employee->created_at->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
