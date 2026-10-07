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
    <div class="col-md-6 mb-3">
        <label class="form-label">Nama Lengkap</label>
        <input type="text" name="name" value="{{ old('name', $employee?->name) }}" class="form-control" required>
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Nomor Induk Karyawan</label>
        <input type="text" name="employee_number" value="{{ old('employee_number', $employee?->employee_number) }}" class="form-control" placeholder="Kosongkan untuk dibuat otomatis (EMP-000001)" {{ $employee ? 'readonly' : '' }}>
        @if (! $employee)
            <div class="form-text">Kosongkan kalau tidak punya nomor khusus — sistem buatkan otomatis.</div>
        @endif
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">NIK KTP (opsional)</label>
        <input type="text" name="nik_ktp" value="{{ old('nik_ktp', $employee?->nik_ktp) }}" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Jenis Kelamin (opsional)</label>
        <select name="gender" class="form-select">
            <option value="">- Pilih -</option>
            <option value="L" @selected(old('gender', $employee?->gender) === 'L')>Laki-laki</option>
            <option value="P" @selected(old('gender', $employee?->gender) === 'P')>Perempuan</option>
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">No. WhatsApp (opsional)</label>
        <input type="text" name="phone" value="{{ old('phone', $employee?->phone) }}" class="form-control" placeholder="08xxxxxxxxxx">
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Tempat Lahir (opsional)</label>
        <input type="text" name="birth_place" value="{{ old('birth_place', $employee?->birth_place) }}" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Lahir (opsional)</label>
        <input type="date" name="birth_date" value="{{ old('birth_date', $employee?->birth_date?->format('Y-m-d')) }}" class="form-control">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Email (opsional)</label>
        <input type="email" name="email" value="{{ old('email', $employee?->email) }}" class="form-control">
    </div>
</div>

<div class="row">
    <div class="col-md-6 mb-3">
        <label class="form-label">Nomor JMO (BPJS Ketenagakerjaan)</label>
        <input type="text" name="no_jmo" value="{{ old('no_jmo', $employee?->no_jmo) }}" class="form-control" placeholder="Nomor akun / ID JMO">
    </div>
    <div class="col-md-6 mb-3">
        <label class="form-label">Nomor KPJ (Kartu Peserta Jamsostek)</label>
        <input type="text" name="no_kpj" value="{{ old('no_kpj', $employee?->no_kpj) }}" class="form-control" placeholder="Nomor KPJ">
    </div>
</div>

<div class="row">
    <div class="col-md-8 mb-3">
        <label class="form-label">Alamat Lengkap (opsional)</label>
        <textarea name="address" rows="2" class="form-control">{{ old('address', $employee?->address) }}</textarea>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Foto 3x4 (opsional)</label>
        @if ($employee?->photo_path)
            <div class="mb-2">
                <img src="{{ $employee->photoUrl() }}" alt="Foto 3x4" class="rounded border" style="width: 72px; height: 96px; object-fit: cover;">
            </div>
        @endif
        <input type="file" name="photo" class="form-control form-control-sm" accept="image/*">
        <div class="form-text">Format JPG/PNG/WEBP, rasio 3x4.</div>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Status Karyawan</label>
        <select name="is_active" class="form-select">
            <option value="1" @selected(old('is_active', $employee?->is_active ?? true) == true)>Aktif</option>
            <option value="0" @selected(old('is_active', $employee?->is_active ?? true) == false)>Nonaktif (Resign)</option>
        </select>
    </div>
</div>

<hr class="my-3">
<h6 class="mb-3">Kepegawaian</h6>
<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Jabatan (opsional)</label>
        <input type="text" name="position" value="{{ old('position', $employee?->position) }}" class="form-control" placeholder="mis. Teknisi Lapangan">
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Departemen</label>
        <select name="department" class="form-select" required>
            @foreach (\App\Models\Employee::DEPARTMENTS as $dept)
                <option value="{{ $dept }}" @selected(old('department', $employee?->department) === $dept)>{{ ucfirst(strtolower($dept)) }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4 mb-3">
        <label class="form-label">Status Kepegawaian</label>
        <select name="employment_status" class="form-select" required>
            @foreach (\App\Models\Employee::EMPLOYMENT_STATUSES as $status)
                <option value="{{ $status }}" @selected(old('employment_status', $employee?->employment_status) === $status)>{{ ucfirst(strtolower($status)) }}</option>
            @endforeach
        </select>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label">Tanggal Masuk (opsional)</label>
        <input type="date" name="join_date" value="{{ old('join_date', $employee?->join_date?->format('Y-m-d')) }}" class="form-control">
    </div>
    <div class="col-md-4 mb-3" id="contract-end-date-field">
        <label class="form-label" id="contract-end-date-label">Tanggal Berakhir Kontrak (opsional)</label>
        <input type="date" name="contract_end_date" value="{{ old('contract_end_date', $employee?->contract_end_date?->format('Y-m-d')) }}" class="form-control">
        <div class="form-text">Dipakai di dokumen PKWT/Perjanjian. Kosongkan untuk karyawan Tetap.</div>
    </div>
    @if ($employee)
        <div class="col-md-4 mb-3">
            <label class="form-label">Tanggal Resign (opsional)</label>
            <input type="date" name="resign_date" value="{{ old('resign_date', $employee?->resign_date?->format('Y-m-d')) }}" class="form-control">
        </div>
    @endif
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <label class="form-label" id="salary-label">Gaji Pokok / Uang Saku (opsional)</label>
        <input type="number" step="0.01" min="0" name="salary" value="{{ old('salary', $employee?->salary) }}" class="form-control">
        <div class="form-text">Dipakai di dokumen PKWT/Perjanjian — bukan untuk perhitungan payroll (belum ada).</div>
    </div>
    <div class="col-md-8 mb-3">
        <label class="form-label">Akun Login Terkait (opsional)</label>
        <select name="user_id" class="form-select">
            <option value="">- Belum punya akun login -</option>
            @foreach ($users as $u)
                <option value="{{ $u->id }}" @selected(old('user_id', $employee?->user_id) == $u->id)>{{ $u->name }} ({{ $u->username }})</option>
            @endforeach
        </select>
        <div class="form-text">Isi kalau karyawan ini juga punya akun login ke REPLAY BOS.</div>
    </div>
</div>

<div class="mb-3">
    <label class="form-label">Catatan (opsional)</label>
    <textarea name="notes" class="form-control" rows="3">{{ old('notes', $employee?->notes) }}</textarea>
</div>

@push('scripts')
<script>
    (function () {
        const statusSelect = document.querySelector('select[name="employment_status"]');
        const endDateField = document.getElementById('contract-end-date-field');
        const endDateLabel = document.getElementById('contract-end-date-label');
        const salaryLabel = document.getElementById('salary-label');

        const labels = {
            KONTRAK: ['Tanggal Berakhir Kontrak (opsional)', 'Gaji Pokok (opsional)'],
            MAGANG: ['Tanggal Berakhir Magang (opsional)', 'Uang Saku (opsional)'],
            PROBATION: ['Tanggal Berakhir Masa Percobaan (opsional)', 'Gaji Pokok (opsional)'],
            TETAP: ['Tanggal Berakhir Kontrak (opsional)', 'Gaji Pokok (opsional)'],
        };

        function toggle() {
            const status = statusSelect.value;
            endDateField.style.display = status === 'TETAP' ? 'none' : '';
            const [endLabel, payLabel] = labels[status] ?? labels.TETAP;
            endDateLabel.textContent = endLabel;
            salaryLabel.textContent = payLabel;
        }

        statusSelect.addEventListener('change', toggle);
        toggle();
    })();
</script>
@endpush
