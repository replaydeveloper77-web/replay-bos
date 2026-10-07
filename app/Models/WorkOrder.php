<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrder extends Model
{
    public const TYPES = ['INSTALASI_BARU', 'RELOKASI', 'LAINNYA'];

    public const STATUSES = ['SCHEDULED', 'IN_PROGRESS', 'DONE', 'CANCELLED'];

    protected $fillable = [
        'work_order_number',
        'customer_id',
        'type',
        'status',
        'assigned_to_id',
        'team_id',
        'scheduled_date',
        'notes',
        'completion_notes',
        'completed_at',
        'completed_by',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_date' => 'date',
            'completed_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function typeLabel(): string
    {
        return static::typeLabelFor($this->type);
    }

    public static function typeLabelFor(string $type): string
    {
        return match ($type) {
            'INSTALASI_BARU' => 'Instalasi Baru',
            'RELOKASI' => 'Relokasi',
            'LAINNYA' => 'Lainnya',
            default => $type,
        };
    }

    public function statusLabel(): string
    {
        return static::statusLabelFor($this->status);
    }

    public static function statusLabelFor(string $status): string
    {
        return match ($status) {
            'SCHEDULED' => 'Dijadwalkan',
            'IN_PROGRESS' => 'Dikerjakan',
            'DONE' => 'Selesai',
            'CANCELLED' => 'Dibatalkan',
            default => $status,
        };
    }
}
