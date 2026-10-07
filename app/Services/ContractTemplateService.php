<?php

namespace App\Services;

use App\Models\ContractTemplate;
use App\Models\Employee;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class ContractTemplateService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function buildCompanyData(): array
    {
        return [
            'name' => Setting::get('company_name', 'PT REPLAY INTI MEDIA'),
            'address' => Setting::get('company_address', 'Dusun Dayueh Rt.006 Rw.002 Ciwiru Pasawahan Kuningan - Jawa Barat 45559'),
            'phone' => Setting::get('company_phone', '(0232) 8613548'),
            'email' => Setting::get('company_email', 'info@replay.co.id'),
            'akta_notaris' => Setting::get('company_akta_notaris', '08'),
            'sk_menkumham' => Setting::get('company_sk_menkumham', 'No. AHU – 00073. AH.02.01 Tahun 2017'),
            'business_field' => Setting::get('company_business_field', 'Internet Service Provider (ISP)'),
            'representative_name' => Setting::get('company_representative_name', ''),
            'representative_title' => Setting::get('company_representative_title', 'Direktur'),
            'representative_ktp' => Setting::get('company_representative_ktp', ''),
        ];
    }

    public function buildLogoDataUri(): ?string
    {
        $logoPath = public_path('images/branding/logo-replay-net-2x.png');

        return file_exists($logoPath)
            ? 'data:image/png;base64,'.base64_encode(file_get_contents($logoPath))
            : null;
    }

    public function documentCode(Employee $employee): string
    {
        return match ($employee->employment_status) {
            'TETAP' => 'PKWTT',
            'MAGANG' => 'MAGANG',
            'PROBATION' => 'PROBATION',
            default => 'PKWT',
        };
    }

    public function documentNumber(Employee $employee): string
    {
        return $employee->employee_number.'/HRD/'.$this->documentCode($employee).'/'.now()->translatedFormat('m').'/'.now()->year;
    }

    /**
     * Builds the token => value map for one employee/company pair — shared by
     * both the real PDF generation path and the unsaved-draft preview path,
     * so a preview is guaranteed to use the exact same substitution logic as
     * the real document.
     */
    public function buildTokens(Employee $employee, array $company): array
    {
        $statusText = match ($employee->employment_status) {
            'KONTRAK' => 'Karyawan dengan Perjanjian Jangka Waktu Tertentu (Kontrak)',
            'MAGANG' => 'Peserta Magang dengan Perjanjian Magang',
            'PROBATION' => 'Karyawan dengan Perjanjian Kerja Masa Percobaan',
            default => 'Karyawan dengan Perjanjian Jangka Waktu Tertentu',
        };

        $dealText = match ($employee->employment_status) {
            'MAGANG' => 'Perjanjian Magang',
            'PROBATION' => 'Perjanjian Kerja Masa Percobaan',
            default => 'Perjanjian Kerja Waktu Tertentu (Kontrak)',
        };

        $isMagang = $employee->employment_status === 'MAGANG';

        return [
            'hari' => now()->translatedFormat('l'),
            'tanggal' => now()->translatedFormat('d'),
            'bulan' => now()->translatedFormat('F'),
            'bulan_angka' => now()->translatedFormat('m'),
            'tahun' => now()->translatedFormat('Y'),
            'tanggal_hari_ini_lengkap' => now()->translatedFormat('l, d F Y'),
            'nama_perusahaan' => $company['name'] ?? '',
            'alamat_perusahaan' => $company['address'] ?? '',
            'bidang_usaha' => $company['business_field'] ?? '',
            'nama_direktur' => $company['representative_name'] ?: '........................',
            'jabatan_direktur' => $company['representative_title'] ?: 'Direktur',
            'ktp_direktur' => $company['representative_ktp'] ?: '........................',
            'nama_karyawan' => $employee->name,
            'jenis_kelamin' => $employee->gender === 'L' ? 'Laki-laki' : ($employee->gender === 'P' ? 'Perempuan' : '........................'),
            'tempat_lahir' => $employee->birth_place ?: '........................',
            'tanggal_lahir' => $employee->birth_date?->translatedFormat('d F Y') ?? '........................',
            'alamat_karyawan' => $employee->address ?: '........................',
            'ktp_karyawan' => $employee->nik_ktp ?: '........................',
            'jabatan_karyawan' => $employee->position ?: '........................',
            'jenis_perjanjian' => $dealText,
            'status_karyawan' => $statusText,
            'sebutan_masa' => $isMagang ? 'masa magang' : 'kontrak',
            'sebutan_jenis' => $isMagang ? 'magang' : 'kontrak',
            'gaji_label' => strtolower($employee->salaryLabel()),
            'gaji_jumlah' => $employee->salary ? number_format($employee->salary, 0, ',', '.') : '........................',
            'tanggal_mulai' => $employee->join_date?->translatedFormat('d F Y') ?? '........................',
            'tanggal_berakhir' => $employee->contract_end_date?->translatedFormat('d F Y') ?? '........................',
        ];
    }

    public function renderBody(string $body, Employee $employee, array $company): string
    {
        $tokens = $this->buildTokens($employee, $company);

        $search = array_map(fn ($key) => '{{'.$key.'}}', array_keys($tokens));

        return str_replace($search, array_values($tokens), $body);
    }

    public function forEmployee(Employee $employee): ContractTemplate
    {
        $type = $employee->employment_status === 'TETAP' ? 'PKWTT' : 'PKWT';

        return ContractTemplate::where('type', $type)->firstOrFail();
    }

    public function update(ContractTemplate $template, string $body, User $actor, Request $request): ContractTemplate
    {
        $old = $template->only(['body']);

        $template->update([
            'body' => $body,
            'updated_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'CONTRACT_TEMPLATE',
            action: 'UPDATE_CONTRACT_TEMPLATE',
            target: 'ContractTemplate',
            targetId: $template->id,
            oldValue: $old,
            newValue: ['body' => $body],
        );

        return $template;
    }
}
