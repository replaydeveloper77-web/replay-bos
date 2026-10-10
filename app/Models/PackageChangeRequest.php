<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageChangeRequest extends Model
{
    public const TYPES = ['UPGRADE', 'DOWNGRADE'];

    public const STATUSES = ['PENDING', 'APPROVED', 'REJECTED', 'CANCELLED'];

    protected $fillable = [
        'request_number',
        'customer_id',
        'type',
        'current_service_package_id',
        'current_package_name',
        'current_price',
        'target_service_package_id',
        'target_package_name',
        'target_price',
        'notes',
        'status',
        'requested_by',
        'approved_by',
        'approved_at',
        'finance_notes',
        'work_order_id',
    ];

    protected function casts(): array
    {
        return [
            'current_price' => 'decimal:2',
            'target_price' => 'decimal:2',
            'approved_at' => 'datetime',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function currentPackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'current_service_package_id');
    }

    public function targetPackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class, 'target_service_package_id');
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function workOrder(): BelongsTo
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    public function isApproved(): bool
    {
        return $this->status === 'APPROVED';
    }

    public function isRejected(): bool
    {
        return $this->status === 'REJECTED';
    }

    public function isCancelled(): bool
    {
        return $this->status === 'CANCELLED';
    }

    public function currentPackageLabel(): string
    {
        return $this->current_package_name
            ?: ($this->currentPackage?->name ?? 'Belum ada paket');
    }

    public function targetPackageLabel(): string
    {
        return $this->target_package_name
            ?: ($this->targetPackage?->name ?? 'Paket Baru');
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'UPGRADE' => 'Upgrade Paket',
            'DOWNGRADE' => 'Downgrade Paket',
            default => $this->type,
        };
    }

    public function typeBadgeClass(): string
    {
        return match ($this->type) {
            'UPGRADE' => 'bg-success-subtle text-success border border-success-subtle',
            'DOWNGRADE' => 'bg-warning-subtle text-warning border border-warning-subtle',
            default => 'bg-secondary-subtle text-secondary',
        };
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            'PENDING' => 'Menunggu Finance',
            'APPROVED' => 'Disetujui',
            'REJECTED' => 'Ditolak',
            'CANCELLED' => 'Dibatalkan',
            default => $this->status,
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'PENDING' => 'bg-warning text-dark',
            'APPROVED' => 'bg-success text-white',
            'REJECTED' => 'bg-danger text-white',
            'CANCELLED' => 'bg-secondary text-white',
            default => 'bg-light text-dark',
        };
    }
}
