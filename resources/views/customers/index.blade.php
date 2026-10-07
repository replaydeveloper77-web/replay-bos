@extends('layouts.app')

@section('title', 'Pelanggan')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">Data Pelanggan</h5>
        @can('create', \App\Models\Customer::class)
            <a href="{{ route('customers.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Tambah Pelanggan
            </a>
        @endcan
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <ul class="nav nav-pills mb-2">
        <li class="nav-item"><a class="nav-link {{ ! request('category') ? 'active' : '' }}" href="{{ route('customers.index', request()->except(['category', 'page'])) }}">Semua</a></li>
        @foreach (\App\Models\Customer::CATEGORIES as $cat)
            <li class="nav-item"><a class="nav-link {{ request('category') === $cat ? 'active' : '' }}" href="{{ route('customers.index', array_merge(request()->except('page'), ['category' => $cat])) }}">{{ ucfirst(strtolower($cat)) }}</a></li>
        @endforeach
    </ul>

    <form method="GET" class="mb-3">
        @if (request('category'))
            <input type="hidden" name="category" value="{{ request('category') }}">
        @endif
        <input type="text" name="q" value="{{ request('q') }}" class="form-control form-control-sm" style="max-width: 300px;" placeholder="Cari nama/perusahaan/no. layanan/IP...">
    </form>

    <div class="noc-stat-card bg-white p-3">
        <div class="table-responsive">
            <table class="table align-middle text-nowrap">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Kategori</th>
                        <th>Perusahaan</th>
                        <th>PIC</th>
                        <th>WhatsApp</th>
                        <th>Layanan</th>
                        <th>No. Layanan</th>
                        <th>IP</th>
                        <th class="text-center">Ticket</th>
                        <th>Status</th>
                        <th style="width: 140px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($customers as $customer)
                        <tr>
                            <td><a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a></td>
                            <td><span class="badge bg-secondary">{{ ucfirst(strtolower($customer->category)) }}</span></td>
                            <td>{{ $customer->company ?? '-' }}</td>
                            <td>{{ $customer->pic_name ?? '-' }}</td>
                            <td>{{ $customer->whatsapp ?? '-' }}</td>
                            <td>{{ $customer->service ?? '-' }}</td>
                            <td>{{ $customer->service_number ?? '-' }}</td>
                            <td class="small">
                                @forelse ($customer->ipAllocations as $ip)
                                    <div><code>{{ $ip->ip_address }}</code></div>
                                @empty
                                    -
                                @endforelse
                            </td>
                            <td class="text-center">{{ $customer->tickets_count }}</td>
                            <td>
                                @if ($customer->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-danger">Nonaktif</span>
                                @endif
                                @if ($customer->isPendingActivation())
                                    <span class="badge bg-warning text-dark">Menunggu Aktivasi</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="d-grid gap-1 flex-grow-1">
                                        @can('activate', \App\Models\Customer::class)
                                            @if ($customer->isPendingActivation())
                                                <form method="POST" action="{{ route('customers.activate', $customer) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-warning w-100" onclick="return confirm('Tandai pelanggan ini sudah selesai diaktivasi?')">Tandai Aktivasi Selesai</button>
                                                </form>
                                            @endif
                                        @endcan
                                        @can('update', \App\Models\Customer::class)
                                            <a href="{{ route('customers.edit', $customer) }}" class="btn btn-sm btn-outline-secondary">Edit</a>
                                            @if ($customer->is_active)
                                                <form method="POST" action="{{ route('customers.disable', $customer) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-danger w-100" onclick="return confirm('Nonaktifkan pelanggan ini?')">Nonaktifkan</button>
                                                </form>
                                            @else
                                                <form method="POST" action="{{ route('customers.enable', $customer) }}">
                                                    @csrf
                                                    <button class="btn btn-sm btn-outline-success w-100">Aktifkan</button>
                                                </form>
                                            @endif
                                        @endcan
                                    </div>
                                    @can('delete', \App\Models\Customer::class)
                                        <form method="POST" action="{{ route('customers.destroy', $customer) }}" class="d-inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pelanggan {{ $customer->name }}? Tindakan ini tidak dapat dibatalkan.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-link text-danger p-0 border-0" title="Hapus Pelanggan" style="line-height: 1;">
                                                <i class="bi bi-trash fs-5 text-danger"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="11" class="text-muted text-center py-3">Belum ada data pelanggan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $customers->links() }}
    </div>
@endsection
