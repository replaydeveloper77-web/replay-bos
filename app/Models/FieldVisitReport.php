<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FieldVisitReport extends Model
{
    public const PURPOSES = ['MAINTENANCE', 'SURVEY', 'KUNJUNGAN_RUTIN', 'LAINNYA'];

    protected $fillable = [
        'report_number',
        'customer_id',
        'location_label',
        'purpose',
        'visit_date',
        'notes',
        'result',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'visit_date' => 'date',
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

    public function photos(): HasMany
    {
        return $this->hasMany(FieldVisitPhoto::class);
    }

    public function locationName(): string
    {
        return $this->customer->name ?? $this->location_label ?? '-';
    }

    public function purposeLabel(): string
    {
        return static::purposeLabelFor($this->purpose);
    }

    public static function purposeLabelFor(string $purpose): string
    {
        return match ($purpose) {
            'MAINTENANCE' => 'Maintenance',
            'SURVEY' => 'Survey Lokasi',
            'KUNJUNGAN_RUTIN' => 'Kunjungan Rutin',
            'LAINNYA' => 'Lainnya',
            default => $purpose,
        };
    }
}
