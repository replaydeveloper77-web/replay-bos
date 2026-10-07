@extends('layouts.app')

@section('title', 'Master Judul Tiket')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h5 class="mb-0">Master Data Judul Tiket</h5>
            <small class="text-muted">Kelola template judul gangguan standar untuk memudahkan pembuatan tiket.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('tickets.create') }}" class="btn btn-outline-primary btn-sm">
                <i class="bi bi-ticket-perforated"></i> Buat Tiket
            </a>
            <a href="{{ route('ticket-titles.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Judul Tiket
            </a>
        </div>
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-2 mb-3">
        <div class="col-md-4">
            <form method="GET" class="input-group input-group-sm">
                <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama judul atau kategori...">
                <button type="submit" class="btn btn-outline-primary"><i class="bi bi-search"></i> Cari</button>
                @if (request('q'))
                    <a href="{{ route('ticket-titles.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                @endif
            </form>
        </div>
    </div>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th style="width: 50px;">#</th>
                        <th>Nama Judul Tiket</th>
                        <th>Kategori Terkait</th>
                        <th>Default Priority</th>
                        <th>Template Deskripsi</th>
                        <th class="text-center" style="width: 100px;">Status</th>
                        <th class="text-end" style="width: 150px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($titles as $index => $item)
                        <tr>
                            <td>{{ $titles->firstItem() + $index }}</td>
                            <td>
                                <strong class="d-block">{{ $item->name }}</strong>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">
                                    {{ \App\Models\Ticket::categoryLabel($item->category) }}
                                </span>
                            </td>
                            <td>
                                @php
                                    $pClass = match ($item->priority) {
                                        'CRITICAL' => 'bg-danger',
                                        'HIGH' => 'bg-warning text-dark',
                                        'MEDIUM' => 'bg-info text-dark',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $pClass }}">{{ $item->priority }}</span>
                            </td>
                            <td class="small text-muted">
                                {{ \Illuminate\Support\Str::limit($item->default_description, 50) ?: '-' }}
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('ticket-titles.toggle', $item) }}" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm p-0 border-0" title="Klik untuk ubah status">
                                        @if ($item->is_active)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">Aktif</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">Nonaktif</span>
                                        @endif
                                    </button>
                                </form>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('ticket-titles.edit', $item) }}" class="btn btn-sm btn-outline-primary py-0 px-2" title="Edit">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                <form method="POST" action="{{ route('ticket-titles.destroy', $item) }}" class="d-inline" onsubmit="return confirm('Hapus master judul tiket ini?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                Belum ada master judul tiket. Klik tombol "+ Tambah Judul Tiket" di atas.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $titles->links() }}
    </div>
@endsection
