<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TroubleshootingSession extends Model
{
    public const CATEGORIES = ['PHYSICAL', 'LOGIC', 'PHYSICAL_LOGIC', 'SOFTWARE', 'HARDWARE', 'POWER'];

    public const STATUSES = ['OPEN', 'CLOSED'];

    public const PHYSICAL_CHECKLIST = [
        'Power', 'Link', 'SFP', 'Optical RX', 'Optical TX', 'Fiber',
        'Patch Cord', 'Connector', 'Port', 'Temperature', 'Hardware',
    ];

    public const LOGIC_CHECKLIST = [
        'VLAN', 'Bridge', 'IP', 'ARP', 'MAC', 'Routing', 'BGP', 'OSPF',
        'Firewall', 'NAT', 'Queue', 'PPPoE', 'DHCP', 'DNS', 'RADIUS',
        'Authentication', 'Service',
    ];

    protected $fillable = [
        'ticket_id',
        'category',
        'physical_checklist',
        'logic_checklist',
        'hypothesis',
        'conclusion',
        'status',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'physical_checklist' => 'array',
            'logic_checklist' => 'array',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function steps(): HasMany
    {
        return $this->hasMany(TroubleshootingStep::class, 'session_id')->orderBy('created_at');
    }
}
