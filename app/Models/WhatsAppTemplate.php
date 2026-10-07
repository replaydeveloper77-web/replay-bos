<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppTemplate extends Model
{
    protected $table = 'whatsapp_templates';

    public const KEYS = ['INCIDENT', 'INVESTIGATING', 'MAINTENANCE', 'MONITORING', 'RESOLVED', 'CLOSED', 'CUSTOM'];

    protected $fillable = [
        'key',
        'name',
        'body',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }
}
