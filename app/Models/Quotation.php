<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Quotation extends Model
{
    public const STATUSES = ['DRAFT', 'SENT', 'ACCEPTED', 'REJECTED', 'CLOSED'];

    protected $fillable = [
        'quotation_number', 'customer_id', 'prospect_name', 'prospect_company',
        'prospect_address', 'prospect_phone', 'latitude', 'longitude',
        'is_covered', 'nearest_distance_km', 'nearest_point_name',
        'subtotal', 'ppn_rate', 'ppn_amount', 'total', 'valid_until', 'notes',
        'status', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'float',
            'longitude' => 'float',
            'is_covered' => 'boolean',
            'nearest_distance_km' => 'float',
            'subtotal' => 'decimal:2',
            'ppn_rate' => 'decimal:2',
            'ppn_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'valid_until' => 'date',
        ];
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class);
    }

    public function invoice(): HasOne
    {
        return $this->hasOne(Invoice::class);
    }
}
