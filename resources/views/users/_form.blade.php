@php
    $assignedRoleIds = $user?->roles->pluck('id')->all() ?? [];
@endphp

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
    <label class="form-label">Nama</label>
    <input type="text" name="name" value="{{ old('name', $user?->name) }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Username</label>
    <input type="text" name="username" value="{{ old('username', $user?->username) }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Email</label>
    <input type="email" name="email" value="{{ old('email', $user?->email) }}" class="form-control" required>
</div>

<div class="mb-3">
    <label class="form-label">Password {{ $user ? '(kosongkan jika tidak diubah)' : '' }}</label>
    <input type="password" name="password" class="form-control" {{ $user ? '' : 'required' }}>
</div>

<div class="mb-3">
    <label class="form-label">Role</label>
    @foreach ($roles as $role)
        <div class="form-check">
            <input type="checkbox" name="role_ids[]" value="{{ $role->id }}" class="form-check-input role-checkbox" id="role-{{ $role->id }}" data-slug="{{ $role->slug }}"
                {{ in_array($role->id, old('role_ids', $assignedRoleIds)) ? 'checked' : '' }}>
            <label class="form-check-label" for="role-{{ $role->id }}">{{ $role->name }}</label>
        </div>
    @endforeach
</div>

<div class="mb-3" id="reseller-link-field" style="display: none;">
    <label class="form-label">Pelanggan Reseller Terkait</label>
    <select name="customer_id" class="form-select">
        <option value="">- Belum ditautkan -</option>
        @foreach ($resellers ?? [] as $reseller)
            <option value="{{ $reseller->id }}" @selected(old('customer_id', $user?->customer_id) == $reseller->id)>{{ $reseller->name }} @if($reseller->company) ({{ $reseller->company }}) @endif</option>
        @endforeach
    </select>
    <div class="form-text">Akun ini cuma bisa lihat data pelanggan Home yang berada di bawah Reseller yang dipilih di sini.</div>
</div>

@push('scripts')
    <script>
        (function () {
            var resellerField = document.getElementById('reseller-link-field');
            var roleCheckboxes = document.querySelectorAll('.role-checkbox');

            function toggleResellerLinkField() {
                var resellerRoleChecked = Array.from(roleCheckboxes).some(function (cb) {
                    return cb.dataset.slug === 'reseller' && cb.checked;
                });
                resellerField.style.display = resellerRoleChecked ? '' : 'none';
            }

            roleCheckboxes.forEach(function (cb) { cb.addEventListener('change', toggleResellerLinkField); });
            toggleResellerLinkField();
        })();
    </script>
@endpush
