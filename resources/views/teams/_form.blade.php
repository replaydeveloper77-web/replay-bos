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
    <label class="form-label">Nama Tim</label>
    <input type="text" name="name" value="{{ old('name', $team?->name) }}" class="form-control" required placeholder="mis. Tim Teknis">
</div>

<div class="mb-3">
    <label class="form-label">Deskripsi (opsional)</label>
    <input type="text" name="description" value="{{ old('description', $team?->description) }}" class="form-control">
</div>

<div class="mb-3">
    <label class="form-label">Anggota</label>
    @php
        $memberIds = old('member_ids', $team?->members->pluck('id')->all() ?? []);
    @endphp
    <div class="row">
        @foreach ($users as $user)
            <div class="col-md-6">
                <div class="form-check">
                    <input type="checkbox" name="member_ids[]" value="{{ $user->id }}" class="form-check-input" id="member-{{ $user->id }}"
                        {{ in_array($user->id, $memberIds) ? 'checked' : '' }}>
                    <label class="form-check-label" for="member-{{ $user->id }}">{{ $user->name }} ({{ $user->username }})</label>
                </div>
            </div>
        @endforeach
    </div>
</div>
