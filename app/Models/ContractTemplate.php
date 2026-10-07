<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ContractTemplate extends Model
{
    public const TYPES = ['PKWT', 'PKWTT'];

    /**
     * Token => human description, shown as a cheat-sheet on the edit page.
     * Keep in sync with ContractTemplateService::buildTokens().
     */
    public const PLACEHOLDERS = [
        'hari' => 'Nama hari hari ini (mis. Kamis)',
        'tanggal' => 'Tanggal hari ini, 2 digit (mis. 01)',
        'bulan' => 'Nama bulan hari ini (mis. Oktober)',
        'bulan_angka' => 'Angka bulan hari ini, 2 digit (mis. 10)',
        'tahun' => 'Tahun hari ini (mis. 2026)',
        'tanggal_hari_ini_lengkap' => 'Tanggal hari ini lengkap (mis. Kamis, 01 Oktober 2026)',
        'nama_perusahaan' => 'Nama perusahaan (dari menu Pengaturan)',
        'alamat_perusahaan' => 'Alamat perusahaan (dari menu Pengaturan)',
        'bidang_usaha' => 'Bidang usaha perusahaan (dari menu Pengaturan)',
        'nama_direktur' => 'Nama perwakilan perusahaan (dari menu Pengaturan)',
        'jabatan_direktur' => 'Jabatan perwakilan perusahaan (dari menu Pengaturan)',
        'ktp_direktur' => 'No. KTP perwakilan perusahaan (dari menu Pengaturan)',
        'nama_karyawan' => 'Nama lengkap karyawan',
        'jenis_kelamin' => 'Laki-laki / Perempuan',
        'tempat_lahir' => 'Tempat lahir karyawan',
        'tanggal_lahir' => 'Tanggal lahir karyawan (mis. 12 Maret 1995)',
        'alamat_karyawan' => 'Alamat karyawan',
        'ktp_karyawan' => 'No. KTP karyawan',
        'jabatan_karyawan' => 'Jabatan/posisi karyawan',
        'jenis_perjanjian' => 'Nama jenis perjanjian sesuai status (Kontrak/Magang/Masa Percobaan)',
        'status_karyawan' => 'Baris status pada Pasal 1 (khusus PKWT)',
        'sebutan_masa' => '"kontrak" atau "masa magang"',
        'sebutan_jenis' => '"kontrak" atau "magang"',
        'gaji_label' => '"gaji pokok" atau "uang saku" (huruf kecil)',
        'gaji_jumlah' => 'Nominal gaji/uang saku, sudah diformat (mis. 3.500.000)',
        'tanggal_mulai' => 'Tanggal mulai kerja karyawan',
        'tanggal_berakhir' => 'Tanggal berakhir kontrak/magang/masa percobaan',
    ];

    protected $fillable = [
        'type',
        'title',
        'body',
        'updated_by',
    ];

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
