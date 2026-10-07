@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
    <h5 class="mb-3">Pengaturan Perusahaan &amp; PPN</h5>

    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert" style="max-width: 640px;">
            <i class="bi bi-check-circle me-1"></i> {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
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

    <div class="noc-stat-card bg-white p-4" style="max-width: 640px;">
        <form method="POST" action="{{ route('settings.update') }}">
            @csrf
            @method('PUT')

            <div class="mb-3">
                <label class="form-label">Nama Perusahaan</label>
                <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name']) }}" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Alamat</label>
                <input type="text" name="company_address" value="{{ old('company_address', $settings['company_address']) }}" class="form-control">
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Telepon/WhatsApp</label>
                    <input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}" class="form-control">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email']) }}" class="form-control">
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Tarif PPN (%)</label>
                    <input type="number" step="0.01" min="0" max="100" name="ppn_rate" value="{{ old('ppn_rate', $settings['ppn_rate']) }}" class="form-control" required>
                    <div class="form-text">Dipakai untuk menghitung harga sesudah PPN di semua Surat Penawaran baru.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Biaya Registrasi Default (Rp)</label>
                    <input type="number" step="0.01" min="0" name="registration_fee" value="{{ old('registration_fee', $settings['registration_fee']) }}" class="form-control" required>
                    <div class="form-text">Otomatis ditambahkan sebagai item di Surat Penawaran baru. Isi 0 kalau tidak mau dipakai. Bisa dihapus/diubah manual per penawaran.</div>
                </div>
            </div>

            <hr class="my-4">
            <h6 class="mb-3">Rekening Pembayaran (tampil di Invoice)</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nama Bank</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $settings['bank_name']) }}" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nomor Rekening</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $settings['bank_account_number']) }}" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Atas Nama</label>
                    <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $settings['bank_account_holder']) }}" class="form-control">
                </div>
            </div>

            <hr class="my-4">
            <h6 class="mb-3">Legalitas Perusahaan (tampil di kop surat PKWT/PKWTT Karyawan)</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Akta Notaris</label>
                    <input type="text" name="company_akta_notaris" value="{{ old('company_akta_notaris', $settings['company_akta_notaris']) }}" class="form-control" placeholder="mis. 08">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">SK Menkumham</label>
                    <input type="text" name="company_sk_menkumham" value="{{ old('company_sk_menkumham', $settings['company_sk_menkumham']) }}" class="form-control" placeholder="mis. No. AHU - 00073. AH.02.01 Tahun 2017">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label">Bidang Usaha</label>
                <input type="text" name="company_business_field" value="{{ old('company_business_field', $settings['company_business_field']) }}" class="form-control">
            </div>

            <hr class="my-4">
            <h6 class="mb-3">Perwakilan Perusahaan (tanda tangan PIHAK PERTAMA di dokumen PKWT/PKWTT)</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nama</label>
                    <input type="text" name="company_representative_name" value="{{ old('company_representative_name', $settings['company_representative_name']) }}" class="form-control">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jabatan</label>
                    <input type="text" name="company_representative_title" value="{{ old('company_representative_title', $settings['company_representative_title']) }}" class="form-control" placeholder="mis. Direktur">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">No. KTP</label>
                    <input type="text" name="company_representative_ktp" value="{{ old('company_representative_ktp', $settings['company_representative_ktp']) }}" class="form-control">
                </div>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    </div>
@endsection
