<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    public const DEPARTMENTS = ['NOC', 'TEKNISI', 'MARKETING', 'FINANCE', 'HR', 'MANAGEMENT', 'OTHER'];

    public const EMPLOYMENT_STATUSES = ['TETAP', 'KONTRAK', 'MAGANG', 'PROBATION'];

    public const GENDERS = ['L', 'P'];

    protected $fillable = [
        'employee_number',
        'name',
        'nik_ktp',
        'gender',
        'birth_place',
        'birth_date',
        'address',
        'phone',
        'email',
        'no_jmo',
        'no_kpj',
        'photo_path',
        'position',
        'department',
        'employment_status',
        'join_date',
        'contract_end_date',
        'salary',
        'resign_date',
        'is_active',
        'user_id',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'birth_date' => 'date',
            'join_date' => 'date',
            'contract_end_date' => 'date',
            'salary' => 'decimal:2',
            'resign_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function photoUrl(): string
    {
        if ($this->photo_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($this->photo_path)) {
            return asset('storage/'.$this->photo_path);
        }

        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&color=4F46E5&background=EEF2FF&size=128';
    }

    public function needsContractEndDate(): bool
    {
        return $this->employment_status !== 'TETAP';
    }

    public function contractDocumentTitle(): string
    {
        return match ($this->employment_status) {
            'TETAP' => 'PERJANJIAN KERJA WAKTU TIDAK TERTENTU (PKWTT)',
            'KONTRAK' => 'PERJANJIAN KERJA WAKTU TERTENTU (PKWT)',
            'MAGANG' => 'PERJANJIAN MAGANG',
            'PROBATION' => 'PERJANJIAN KERJA MASA PERCOBAAN',
            default => 'PERJANJIAN KERJA',
        };
    }

    public function contractPartyLabel(): string
    {
        return $this->employment_status === 'MAGANG' ? 'Peserta Magang' : 'Karyawan';
    }

    public function salaryLabel(): string
    {
        return $this->employment_status === 'MAGANG' ? 'Uang Saku' : 'Gaji Pokok';
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    public function leaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class);
    }
}
