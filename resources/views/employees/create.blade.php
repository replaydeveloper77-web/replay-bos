@extends('layouts.app')

@section('title', 'Tambah Karyawan')

@section('content')
    <h5 class="mb-3">Tambah Karyawan</h5>

    <div class="noc-stat-card bg-white p-4" style="max-width: 720px;">
        <form method="POST" action="{{ route('employees.store') }}" enctype="multipart/form-data">
            @csrf
            @include('employees._form', ['employee' => null])
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('employees.index') }}" class="btn btn-outline-secondary">Batal</a>
        </form>
    </div>
@endsection
