@extends('layouts.app')

@section('title', 'Edit Redaksi — ' . $template->title)

@section('content')
    <h5 class="mb-3">Edit Redaksi — {{ $template->title }}</h5>

    @if (session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="noc-stat-card bg-white p-4">
                <form method="POST" action="{{ route('contract-templates.update', $template->type) }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Isi Redaksi (HTML)</label>
                        <textarea name="body" id="body-textarea" class="form-control" rows="28" style="font-family: monospace; font-size: 12px;" required>{{ old('body', $template->body) }}</textarea>
                        <div class="form-text">
                            Ini kode HTML mentah — tag seperti &lt;p&gt;, &lt;ol&gt;, &lt;li&gt;, &lt;div class="pasal-title"&gt; dipertahankan.
                            Pakai token <code>@{{nama_token}}</code> di mana pun untuk data yang otomatis terisi — lihat daftar di samping.
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Redaksi</button>
                    <a href="{{ route('contract-templates.index') }}" class="btn btn-outline-secondary">Batal</a>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="noc-stat-card bg-white p-4 mb-3">
                <h6 class="mb-3">Pratinjau PDF</h6>
                <p class="small text-muted">Lihat dulu tampilan PDF dari isi di kotak redaksi sebelah, SEBELUM disimpan. Dibuka di tab baru.</p>
                <form method="POST" action="{{ route('contract-templates.preview', $template->type) }}" target="_blank" onsubmit="document.getElementById('preview-body').value = document.getElementById('body-textarea').value">
                    @csrf
                    <input type="hidden" name="body" id="preview-body">
                    <div class="mb-3">
                        <label class="form-label">Pratinjau dengan data</label>
                        <select name="employee_id" class="form-select">
                            <option value="">- Data Contoh (bukan karyawan asli) -</option>
                            @foreach ($employees as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->name }} ({{ ucfirst(strtolower($emp->employment_status)) }})</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-outline-dark w-100">
                        <i class="bi bi-eye"></i> Pratinjau PDF
                    </button>
                </form>
            </div>

            <div class="noc-stat-card bg-white p-4">
                <h6 class="mb-3">Daftar Token</h6>
                <div class="table-responsive" style="max-height: 500px; overflow-y: auto;">
                    <table class="table table-sm">
                        <tbody>
                            @foreach ($placeholders as $placeholder)
                                <tr>
                                    <td><code>{{ $placeholder['display'] }}</code></td>
                                    <td class="small text-muted">{{ $placeholder['description'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
