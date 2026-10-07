@extends('layouts.app')

@section('title', 'Edit Jenis Aset')

@section('content')
    <h5 class="mb-3">Edit Jenis Aset</h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('asset-types.update', $assetType) }}">
            @csrf
            @method('PUT')
            @include('asset-types._form')
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('asset-types.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
