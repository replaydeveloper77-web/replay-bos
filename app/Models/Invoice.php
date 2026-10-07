<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    public const TYPES = ['CLOSING', 'RECURRING', 'UPGRADE', 'MANUAL'];

    public const STATUSES = ['UNPAID', 'PAID'];

    protected $fillable = [
        'invoice_number', 'quotation_id', 'customer_id', 'type', 'billing_period', 'due_date',
        'subtotal', 'ppn_rate', 'ppn_amount', 'total', 'status', 'paid_at', 'paid_by', 'notes', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'ppn_rate' => 'decimal:2',
            'ppn_amount' => 'decimal:2',
            'total' => 'decimal:2',
            'due_date' => 'date',
            'paid_at' => 'datetime',
        ];
    }

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function isPaid(): bool
    {
        return $this->status === 'PAID';
    }

    public function isOverdue(): bool
    {
        return ! $this->isPaid() && $this->due_date !== null && $this->due_date->isPast();
    }

    public function typeLabel(): string
    {
        return match ($this->type) {
            'CLOSING' => 'Closing (Pelanggan Baru)',
            'RECURRING' => 'Tagihan Bulanan',
            'UPGRADE' => 'Upgrade Paket',
            default => 'Manual',
        };
    }
}
