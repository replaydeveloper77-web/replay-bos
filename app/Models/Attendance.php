<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'check_in_at',
        'check_in_lat',
        'check_in_lng',
        'check_in_photo_path',
        'check_in_address',
        'check_out_at',
        'check_out_lat',
        'check_out_lng',
        'check_out_photo_path',
        'check_out_address',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
            'check_in_at' => 'datetime',
            'check_out_at' => 'datetime',
            'check_in_lat' => 'decimal:7',
            'check_in_lng' => 'decimal:7',
            'check_out_lat' => 'decimal:7',
            'check_out_lng' => 'decimal:7',
        ];
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function isCheckedIn(): bool
    {
        return $this->check_in_at !== null;
    }

    public function isCheckedOut(): bool
    {
        return $this->check_out_at !== null;
    }

    public function checkInPhotoUrl(): ?string
    {
        return $this->check_in_photo_path ? asset('storage/'.$this->check_in_photo_path) : null;
    }

    public function checkOutPhotoUrl(): ?string
    {
        return $this->check_out_photo_path ? asset('storage/'.$this->check_out_photo_path) : null;
    }
}
