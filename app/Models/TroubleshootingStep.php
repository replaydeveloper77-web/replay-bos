<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TroubleshootingStep extends Model
{
    public const STEP_TYPES = ['OBSERVATION', 'EVIDENCE', 'HYPOTHESIS', 'ACTION', 'VERIFICATION', 'DOCUMENTATION'];

    public $timestamps = false;

    protected $fillable = [
        'session_id',
        'step_type',
        'description',
        'created_by',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
        ];
    }

    public function session(): BelongsTo
    {
        return $this->belongsTo(TroubleshootingSession::class, 'session_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
