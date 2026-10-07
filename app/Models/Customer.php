<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    public const ACTIVATION_STATUSES = ['PENDING', 'ACTIVE'];

    public const CATEGORIES = ['RESELLER', 'CORPORATE', 'HOME'];

    protected $fillable = [
        'name',
        'category',
        'reseller_id',
        'company',
        'pic_name',
        'whatsapp',
        'email',
        'location',
        'service',
        'service_number',
        'service_package_id',
        'subscription_price',
        'billing_due_day',
        'notes',
        'is_active',
        'activation_status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'subscription_price' => 'decimal:2',
        ];
    }

    public function isPendingActivation(): bool
    {
        return $this->activation_status === 'PENDING';
    }

    public function hasBilling(): bool
    {
        return $this->subscription_price !== null && (float) $this->subscription_price > 0;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }

    public function ipAllocations(): HasMany
    {
        return $this->hasMany(CustomerIpAllocation::class)->orderBy('ip_address');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function quotations(): HasMany
    {
        return $this->hasMany(Quotation::class);
    }

    public function workOrders(): HasMany
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function hasHistory(): bool
    {
        return $this->tickets()->exists() || $this->quotations()->exists() || $this->invoices()->exists() || $this->workOrders()->exists();
    }

    /**
     * The Reseller this customer belongs to — only meaningful when category = HOME.
     */
    public function reseller(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'reseller_id');
    }

    /**
     * Home customers under this Reseller — only meaningful when category = RESELLER.
     */
    public function homeCustomers(): HasMany
    {
        return $this->hasMany(Customer::class, 'reseller_id');
    }
}
