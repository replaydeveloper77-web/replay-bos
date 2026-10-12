@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
        <div>
            <h5 class="mb-0 fw-bold">Roles &amp; Permissions</h5>
            <div class="text-muted small">Kelola tingkatan hak akses peran (role) dan perizinan fitur (permission) operasional sistem.</div>
        </div>
        @if (auth()->user()?->hasRole('super-admin') || auth()->user()?->can('create', \App\Models\Role::class))
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-outline-primary btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPermissionModal">
                    <i class="bi bi-shield-plus"></i>
                    <span>Tambah Permission</span>
                </button>
                <button type="button" class="btn btn-primary btn-sm d-flex align-items-center gap-1 shadow-sm" data-bs-toggle="modal" data-bs-target="#addRoleModal">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Role</span>
                </button>
            </div>
        @endif
    </div>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3 shadow-sm" role="alert">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show mb-3 shadow-sm" role="alert">
            <div class="fw-semibold mb-1"><i class="bi bi-exclamation-triangle me-1"></i> Terjadi kesalahan saat memproses data:</div>
            <ul class="mb-0 small ps-3">
                @foreach ($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Nav Tabs: Daftar Role & Daftar Permission -->
    <ul class="nav nav-pills mb-3 gap-2" id="rolePermissionTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active px-3 py-2 fw-semibold d-flex align-items-center gap-2" id="roles-tab" data-bs-toggle="pill" data-bs-target="#roles-tab-pane" type="button" role="tab" aria-controls="roles-tab-pane" aria-selected="true">
                <i class="bi bi-person-badge"></i>
                <span>Daftar Role</span>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">{{ $roles->count() }}</span>
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link px-3 py-2 fw-semibold d-flex align-items-center gap-2" id="permissions-tab" data-bs-toggle="pill" data-bs-target="#permissions-tab-pane" type="button" role="tab" aria-controls="permissions-tab-pane" aria-selected="false">
                <i class="bi bi-shield-lock"></i>
                <span>Daftar Permission</span>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">{{ $allPermissions->count() }}</span>
            </button>
        </li>
    </ul>

    <div class="tab-content" id="rolePermissionTabContent">
        <!-- TAB 1: DAFTAR ROLE -->
        <div class="tab-pane fade show active" id="roles-tab-pane" role="tabpanel" aria-labelledby="roles-tab" tabindex="0">
            <div class="noc-stat-card bg-white p-3 shadow-sm rounded-3">
                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 18%;">Role</th>
                                <th>Deskripsi</th>
                                <th class="text-center" style="width: 12%;">Jumlah User</th>
                                <th class="text-center" style="width: 14%;">Jumlah Permission</th>
                                <th class="text-end" style="width: 16%;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($roles as $role)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-dark">{{ $role->name }}</div>
                                        <code class="small text-muted">{{ $role->slug }}</code>
                                    </td>
                                    <td class="text-secondary small">{{ $role->description ?: '—' }}</td>
                                    <td class="text-center">
                                        <span class="badge bg-light text-dark border px-2 py-1">{{ $role->users_count }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 fw-semibold">{{ $role->permissions_count }}</span>
                                    </td>
                                    <td class="text-end">
                                        @can('manage', \App\Models\Role::class)
                                            <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1">
                                                <i class="bi bi-sliders"></i>
                                                <span>Kelola Permission</span>
                                            </a>
                                        @endcan
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada role terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB 2: DAFTAR PERMISSION -->
        <div class="tab-pane fade" id="permissions-tab-pane" role="tabpanel" aria-labelledby="permissions-tab" tabindex="0">
            <div class="noc-stat-card bg-white p-3 shadow-sm rounded-3">
                <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2 mb-3">
                    <div class="input-group input-group-sm" style="max-width: 320px;">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" id="permissionSearchInput" class="form-control border-start-0 ps-0" placeholder="Cari permission atau modul...">
                    </div>
                    <div class="small text-muted">
                        Total: <strong>{{ $allPermissions->count() }}</strong> permission dalam <strong>{{ count($modules) }}</strong> modul
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover mb-0" id="permissionsTable">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 14%;">Modul</th>
                                <th style="width: 22%;">Nama Permission</th>
                                <th style="width: 20%;">Kode Slug</th>
                                <th>Deskripsi</th>
                                <th style="width: 24%;">Role yang Memiliki</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($allPermissions as $perm)
                                <tr class="perm-row" data-search="{{ strtolower($perm->module . ' ' . $perm->name . ' ' . $perm->slug . ' ' . $perm->description) }}">
                                    <td>
                                        <span class="badge bg-dark-subtle text-dark border px-2 py-1 text-uppercase font-monospace" style="font-size: 0.72rem;">{{ $perm->module }}</span>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $perm->name }}</div>
                                    </td>
                                    <td>
                                        <code class="text-primary">{{ $perm->slug }}</code>
                                    </td>
                                    <td class="text-muted small">
                                        {{ $perm->description ?: '—' }}
                                    </td>
                                    <td>
                                        <div class="d-flex flex-wrap gap-1">
                                            @forelse ($perm->roles as $r)
                                                <span class="badge bg-light text-secondary border px-2 py-1" style="font-size: 0.72rem;">{{ $r->name }}</span>
                                            @empty
                                                <span class="text-muted fst-italic small">Belum di-assign</span>
                                            @endforelse
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">Belum ada permission terdaftar.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <style>
        .modal-dialog-scrollable .modal-content {
            max-height: calc(100vh - 3.5rem);
        }
        .modal-dialog-scrollable .modal-body {
            overflow-y: auto;
        }
        .perm-scroll-container {
            max-height: 320px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #cbd5e1 #f8fafc;
        }
        .perm-scroll-container::-webkit-scrollbar {
            width: 6px;
        }
        .perm-scroll-container::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }
        .cursor-pointer {
            cursor: pointer;
        }
        .perm-card-check {
            transition: all 0.15s ease-in-out;
        }
        .perm-card-check:hover {
            background-color: #f1f5f9;
            border-color: #cbd5e1 !important;
        }
    </style>

    @if (auth()->user()?->hasRole('super-admin') || auth()->user()?->can('create', \App\Models\Role::class))
        <!-- MODAL TAMBAH ROLE -->
        <div class="modal fade" id="addRoleModal" tabindex="-1" aria-labelledby="addRoleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <form method="POST" action="{{ route('roles.store') }}" id="addRoleForm" class="modal-content border-0 shadow">
                    @csrf
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="addRoleModalLabel">
                                <i class="bi bi-person-plus-fill text-primary"></i>
                                <span>Tambah Role Baru</span>
                            </h5>
                            <div class="text-muted small">Definisikan peran baru dalam organisasi Replay BOS beserta hak aksesnya.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="roleNameInput" class="form-label fw-semibold">Nama Role <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="roleNameInput" class="form-control" placeholder="misal: Teknisi OSP, Billing Support" value="{{ old('name') }}" required>
                                <div class="form-text small">Nama resmi peran jabatan yang ditampilkan pada sistem.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="roleSlugInput" class="form-label fw-semibold">Kode / Slug Unik</label>
                                <input type="text" name="slug" id="roleSlugInput" class="form-control font-monospace" placeholder="misal: teknisi-osp (otomatis jika kosong)" value="{{ old('slug') }}">
                                <div class="form-text small">Identifier unik (huruf kecil &amp; tanda strip). Otomatis diisi jika kosong.</div>
                            </div>
                            <div class="col-12">
                                <label for="roleDescInput" class="form-label fw-semibold">Deskripsi Tugas &amp; Tanggung Jawab</label>
                                <textarea name="description" id="roleDescInput" rows="2" class="form-control" placeholder="Rangkuman ruang lingkup operasional role ini...">{{ old('description') }}</textarea>
                            </div>
                        </div>

                        <hr class="my-3 text-muted">

                        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
                            <div>
                                <div class="fw-bold text-dark"><i class="bi bi-shield-check me-1 text-primary"></i> Hak Akses / Permission Awal (Opsional)</div>
                                <div class="small text-muted">Centang hak akses yang langsung diberikan ke role baru ini:</div>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAllRolePerms">
                                    <i class="bi bi-check2-all me-1"></i> Pilih Semua
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeselectAllRolePerms">
                                    <i class="bi bi-x-circle me-1"></i> Batal Semua
                                </button>
                            </div>
                        </div>

                        <!-- Pencarian Cepat Permission -->
                        <div class="input-group input-group-sm mb-2 shadow-sm">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" id="rolePermQuickSearch" class="form-control border-start-0 ps-0" placeholder="Ketik untuk mencari permission atau modul (misal: ticket, device, user)...">
                            <button class="btn btn-outline-secondary" type="button" id="btnClearPermSearch" title="Reset pencarian"><i class="bi bi-x-lg"></i></button>
                        </div>

                        <!-- Container Scrollable Permissions Bebas Overflow -->
                        <div class="perm-scroll-container border rounded-3 p-3 bg-light">
                            @foreach ($permissionsByModule as $module => $perms)
                                <div class="card mb-3 border shadow-none perm-module-card" data-module-name="{{ strtolower($module) }}">
                                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-2 px-3 border-bottom">
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-dark-subtle text-dark border px-2 py-1 text-uppercase font-monospace" style="font-size: 0.72rem;">{{ $module }}</span>
                                            <span class="small text-muted">({{ $perms->count() }} permission)</span>
                                        </div>
                                        <button type="button" class="btn btn-link btn-sm text-decoration-none p-0 fw-semibold btn-select-module" data-module="{{ Str::slug($module) }}" style="font-size: 0.75rem;">
                                            Centang Semua Modul
                                        </button>
                                    </div>
                                    <div class="card-body p-2 bg-white">
                                        <div class="row g-2">
                                            @foreach ($perms as $p)
                                                <div class="col-sm-6 perm-item" data-perm-search="{{ strtolower($p->name . ' ' . $p->slug . ' ' . $module) }}">
                                                    <div class="form-check p-2 border rounded perm-card-check h-100 d-flex align-items-start gap-2 m-0 cursor-pointer">
                                                        <input class="form-check-input mt-1 ms-0 perm-checkbox perm-module-{{ Str::slug($module) }}" type="checkbox" name="permission_ids[]" value="{{ $p->id }}" id="role-perm-{{ $p->id }}" {{ in_array($p->id, old('permission_ids', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label small cursor-pointer flex-grow-1" for="role-perm-{{ $p->id }}">
                                                            <span class="fw-semibold text-dark d-block">{{ $p->name }}</span>
                                                            <code class="text-primary" style="font-size: 0.72rem;">{{ $p->slug }}</code>
                                                        </label>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                            <div id="noPermFoundNotice" class="text-center py-3 text-muted small d-none">
                                <i class="bi bi-info-circle me-1"></i> Tidak ada permission yang cocok dengan kata kunci pencarian.
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Role
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL TAMBAH PERMISSION -->
        <div class="modal fade" id="addPermissionModal" tabindex="-1" aria-labelledby="addPermissionModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-md modal-dialog-centered modal-dialog-scrollable">
                <form method="POST" action="{{ route('permissions.store') }}" id="addPermissionForm" class="modal-content border-0 shadow">
                    @csrf
                    <div class="modal-header bg-light">
                        <div>
                            <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="addPermissionModalLabel">
                                <i class="bi bi-shield-plus text-primary"></i>
                                <span>Tambah Permission Baru</span>
                            </h5>
                            <div class="text-muted small">Daftarkan hak akses permission baru untuk fitur atau modul sistem.</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="mb-3">
                            <label for="permNameInput" class="form-label fw-semibold">Nama Permission <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="permNameInput" class="form-control" placeholder="misal: Export Laporan Tiket, Restart OLT" value="{{ old('name') }}" required>
                            <div class="form-text small">Label deskriptif aksi yang mudah dipahami.</div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="permModuleInput" class="form-label fw-semibold">Kategori Modul <span class="text-danger">*</span></label>
                                <input type="text" name="module" id="permModuleInput" class="form-control" list="moduleDatalist" placeholder="misal: ticket, device" value="{{ old('module') }}" required>
                                <datalist id="moduleDatalist">
                                    @foreach ($modules as $mod)
                                        <option value="{{ $mod }}"></option>
                                    @endforeach
                                </datalist>
                                <div class="form-text small">Pilih modul yang ada atau ketik modul baru.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="permSlugInput" class="form-label fw-semibold">Kode Slug <span class="text-danger">*</span></label>
                                <input type="text" name="slug" id="permSlugInput" class="form-control font-monospace" placeholder="misal: ticket.export" value="{{ old('slug') }}" required>
                                <div class="form-text small">Format standar: <code>modul.aksi</code>.</div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="permDescInput" class="form-label fw-semibold">Deskripsi Permission</label>
                            <textarea name="description" id="permDescInput" rows="2" class="form-control" placeholder="Penjelasan mengenai hak akses ini...">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-1">
                            <label class="form-label fw-semibold d-flex justify-content-between align-items-center">
                                <span>Terapkan ke Role</span>
                                <span class="small text-muted">Pilih role yang langsung memiliki izin ini</span>
                            </label>
                            <div class="p-3 bg-light border rounded-3" style="max-height: 180px; overflow-y: auto;">
                                <div class="row g-2">
                                    @foreach ($roles as $r)
                                        <div class="col-sm-6">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="role_ids[]" value="{{ $r->id }}" id="perm-role-{{ $r->id }}" {{ $r->slug === 'super-admin' || in_array($r->id, old('role_ids', [])) ? 'checked' : '' }}>
                                                <label class="form-check-label small" for="perm-role-{{ $r->id }}">
                                                    <span class="fw-semibold">{{ $r->name }}</span>
                                                    @if ($r->slug === 'super-admin')
                                                        <span class="badge bg-primary-subtle text-primary border" style="font-size: 0.65rem;">Default</span>
                                                    @endif
                                                </label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary d-flex align-items-center gap-1 shadow-sm">
                            <i class="bi bi-save me-1"></i> Simpan Permission
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // 1. Search filter untuk tabel Permission (Tab 2)
            const searchInput = document.getElementById('permissionSearchInput');
            if (searchInput) {
                searchInput.addEventListener('input', function () {
                    const q = this.value.toLowerCase().trim();
                    const rows = document.querySelectorAll('.perm-row');
                    rows.forEach(row => {
                        const searchData = row.getAttribute('data-search') || '';
                        row.style.display = searchData.includes(q) ? '' : 'none';
                    });
                });
            }

            // 2. Pencarian cepat permission di dalam Modal Tambah Role
            const rolePermSearch = document.getElementById('rolePermQuickSearch');
            const btnClearPermSearch = document.getElementById('btnClearPermSearch');
            const noPermNotice = document.getElementById('noPermFoundNotice');

            function filterRolePermissions(keyword) {
                const q = keyword.toLowerCase().trim();
                let visibleCount = 0;

                document.querySelectorAll('.perm-module-card').forEach(card => {
                    let moduleHasVisible = false;
                    card.querySelectorAll('.perm-item').forEach(item => {
                        const searchData = item.getAttribute('data-perm-search') || '';
                        const matches = !q || searchData.includes(q);
                        item.style.display = matches ? '' : 'none';
                        if (matches) {
                            moduleHasVisible = true;
                            visibleCount++;
                        }
                    });
                    card.style.display = moduleHasVisible ? '' : 'none';
                });

                if (noPermNotice) {
                    noPermNotice.classList.toggle('d-none', visibleCount > 0);
                }
            }

            if (rolePermSearch) {
                rolePermSearch.addEventListener('input', function () {
                    filterRolePermissions(this.value);
                });
            }

            if (btnClearPermSearch && rolePermSearch) {
                btnClearPermSearch.addEventListener('click', function () {
                    rolePermSearch.value = '';
                    filterRolePermissions('');
                });
            }

            // 3. Auto-slug generator untuk Tambah Role
            const roleNameInput = document.getElementById('roleNameInput');
            const roleSlugInput = document.getElementById('roleSlugInput');
            let isRoleSlugManuallyEdited = false;

            if (roleSlugInput) {
                roleSlugInput.addEventListener('input', function () {
                    isRoleSlugManuallyEdited = this.value.trim().length > 0;
                });
            }

            if (roleNameInput && roleSlugInput) {
                roleNameInput.addEventListener('input', function () {
                    if (!isRoleSlugManuallyEdited) {
                        const slug = this.value
                            .toLowerCase()
                            .trim()
                            .replace(/[^\w\s-]/g, '')
                            .replace(/[\s_-]+/g, '-')
                            .replace(/^-+|-+$/g, '');
                        roleSlugInput.value = slug;
                    }
                });
            }

            // 4. Auto-slug helper untuk Tambah Permission dari Modul & Nama
            const permNameInput = document.getElementById('permNameInput');
            const permModuleInput = document.getElementById('permModuleInput');
            const permSlugInput = document.getElementById('permSlugInput');
            let isPermSlugManuallyEdited = false;

            if (permSlugInput) {
                permSlugInput.addEventListener('input', function () {
                    isPermSlugManuallyEdited = this.value.trim().length > 0;
                });
            }

            function updatePermSlug() {
                if (!isPermSlugManuallyEdited && permSlugInput) {
                    const mod = (permModuleInput?.value || '').toLowerCase().trim().replace(/[^\w-]/g, '');
                    const name = (permNameInput?.value || '')
                        .toLowerCase()
                        .trim()
                        .replace(/[^\w\s-]/g, '')
                        .replace(/[\s_-]+/g, '.');
                    if (mod && name) {
                        permSlugInput.value = `${mod}.${name}`;
                    } else if (name) {
                        permSlugInput.value = name;
                    }
                }
            }

            if (permNameInput) permNameInput.addEventListener('input', updatePermSlug);
            if (permModuleInput) permModuleInput.addEventListener('input', updatePermSlug);

            // 5. Tombol Pilih Semua & Batal Semua Permissions pada Modal Tambah Role
            const btnSelectAll = document.getElementById('btnSelectAllRolePerms');
            const btnDeselectAll = document.getElementById('btnDeselectAllRolePerms');

            if (btnSelectAll) {
                btnSelectAll.addEventListener('click', function () {
                    document.querySelectorAll('.perm-checkbox').forEach(cb => {
                        // Hanya centang item yang sedang terlihat (sesuai filter pencarian)
                        const permItem = cb.closest('.perm-item');
                        if (!permItem || permItem.style.display !== 'none') {
                            cb.checked = true;
                        }
                    });
                });
            }

            if (btnDeselectAll) {
                btnDeselectAll.addEventListener('click', function () {
                    document.querySelectorAll('.perm-checkbox').forEach(cb => {
                        const permItem = cb.closest('.perm-item');
                        if (!permItem || permItem.style.display !== 'none') {
                            cb.checked = false;
                        }
                    });
                });
            }

            // 6. Centang Semua per Modul pada Modal Tambah Role
            document.querySelectorAll('.btn-select-module').forEach(btn => {
                btn.addEventListener('click', function () {
                    const mod = this.getAttribute('data-module');
                    const checkboxes = document.querySelectorAll(`.perm-module-${mod}`);
                    const anyUnchecked = Array.from(checkboxes).some(cb => !cb.checked);
                    checkboxes.forEach(cb => cb.checked = anyUnchecked);
                    this.textContent = anyUnchecked ? 'Batal Centang Modul' : 'Centang Semua Modul';
                });
            });
        });
    </script>
@endsection

