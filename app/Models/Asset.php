<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    public const STATUSES = ['IN_STOCK', 'INSTALLED', 'DAMAGED', 'RETURNED', 'DISPOSED'];

    protected $fillable = [
        'asset_code',
        'asset_type_id',
        'serial_number',
        'status',
        'customer_id',
        'coverage_point_id',
        'location_note',
        'purchase_date',
        'price',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'purchase_date' => 'date',
            'price' => 'decimal:2',
        ];
    }

    public function assetType(): BelongsTo
    {
        return $this->belongsTo(AssetType::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function coveragePoint(): BelongsTo
    {
        return $this->belongsTo(CoveragePoint::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusLabel(): string
    {
        return static::labelFor($this->status);
    }

    public static function labelFor(string $status): string
    {
        return match ($status) {
            'IN_STOCK' => 'Di Gudang',
            'INSTALLED' => 'Terpasang',
            'DAMAGED' => 'Rusak',
            'RETURNED' => 'Dikembalikan',
            'DISPOSED' => 'Dibuang/Afkir',
            default => $status,
        };
    }
}
