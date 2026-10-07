@extends('layouts.app')

@section('title', 'Kelola Permission')

@section('content')
    <h5 class="mb-3">Kelola Permission — {{ $role->name }}</h5>

    <div class="noc-stat-card bg-white p-4">
        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf
            @method('PUT')

            @foreach ($permissionsByModule as $module => $permissions)
                <h6 class="text-uppercase text-muted small mt-3">{{ $module }}</h6>
                <div class="row">
                    @foreach ($permissions as $permission)
                        <div class="col-sm-6 col-md-4">
                            <div class="form-check">
                                <input type="checkbox" name="permission_ids[]" value="{{ $permission->id }}" class="form-check-input" id="perm-{{ $permission->id }}"
                                    {{ in_array($permission->id, old('permission_ids', $assignedIds)) ? 'checked' : '' }}>
                                <label class="form-check-label" for="perm-{{ $permission->id }}">{{ $permission->name }}</label>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endforeach

            <div class="mt-4">
                <button type="submit" class="btn btn-primary">Simpan Permission</button>
                <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">Batal</a>
            </div>
        </form>
    </div>
@endsection
