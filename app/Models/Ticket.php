<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    public const PRIORITIES = ['CRITICAL', 'HIGH', 'MEDIUM', 'LOW'];

    public const STATUSES = [
        'OPEN', 'ACKNOWLEDGED', 'INVESTIGATING', 'HANDLE', 'WAITING',
        'ESCALATED', 'MONITORING', 'RESOLVED', 'CLOSED',
    ];

    public const CATEGORIES = [
        'LINK_DOWN',
        'GANGGUAN_BANDWIDTH',
        'HIGH_LATENCY_INTERMITTEN',
        'AKTIVASI',
        'DISMANTLE',
        'UPGRADE',
        'DOWNGRADE',
        'NON_GANGGUAN',
        'BACKBONE',
        'SERVER',
        'NETWORK',
        'ROUTING',
        'BGP',
        'OSPF',
        'VLAN',
        'FIREWALL',
        'INTERNET',
        'PPPOE',
        'DHCP',
        'DNS',
        'RADIUS',
        'OLT',
        'FTTH',
        'POWER',
        'HARDWARE',
        'SOFTWARE',
        'SECURITY',
        'HANDLE',
        'OTHER',
    ];

    public static function categoryLabel(?string $category): string
    {
        if (! $category) {
            return '-';
        }

        return match (strtoupper(str_replace([' ', '/', '-'], '_', $category))) {
            'LINK_DOWN' => 'Link Down',
            'GANGGUAN_BANDWIDTH' => 'Gangguan Bandwidth',
            'HIGH_LATENCY_INTERMITTEN', 'HIGH_LATENCY_INTERMITTENT' => 'High Latency / Intermitten',
            'AKTIVASI' => 'Aktivasi',
            'DISMANTLE' => 'Dismantle',
            'UPGRADE' => 'Upgrade',
            'DOWNGRADE' => 'Downgrade',
            'NON_GANGGUAN' => 'Non Gangguan',
            'HANDLE' => 'Handle',
            'BACKBONE' => 'Backbone',
            'SERVER' => 'Server',
            'NETWORK' => 'Network',
            'ROUTING' => 'Routing',
            'BGP' => 'BGP',
            'OSPF' => 'OSPF',
            'VLAN' => 'VLAN',
            'FIREWALL' => 'Firewall',
            'INTERNET' => 'Internet',
            'PPPOE' => 'PPPoE',
            'DHCP' => 'DHCP',
            'DNS' => 'DNS',
            'RADIUS' => 'RADIUS',
            'OLT' => 'OLT',
            'FTTH' => 'FTTH',
            'POWER' => 'Power',
            'HARDWARE' => 'Hardware',
            'SOFTWARE' => 'Software',
            'SECURITY' => 'Security',
            'OTHER' => 'Other',
            default => ucwords(strtolower(str_replace('_', ' ', $category))),
        };
    }

    public const CLOSED_STATUSES = ['RESOLVED', 'CLOSED'];

    public const ESCALATION_LEVELS = ['NOC_LEVEL_1', 'NOC_LEVEL_2', 'NETWORK_ENGINEER', 'SENIOR_ENGINEER', 'VENDOR'];

    public const PREFIX_RESELLER = 'RS-';
    public const PREFIX_CORPORATE = 'CP-';
    public const PREFIX_HOME = 'RH-';

    public static function prefixForCategory(?string $category): string
    {
        return match (strtoupper($category ?? 'HOME')) {
            'RESELLER' => self::PREFIX_RESELLER,
            'CORPORATE' => self::PREFIX_CORPORATE,
            default => self::PREFIX_HOME,
        };
    }

    protected $fillable = [
        'ticket_number',
        'public_token',
        'title',
        'description',
        'category',
        'subcategory',
        'priority',
        'status',
        'escalation_level',
        'device',
        'location',
        'service',
        'customer_name',
        'customer_phone',
        'customer_id',
        'reporter_id',
        'pic_id',
        'team_id',
        'created_by',
        'sla_response_minutes',
        'sla_due_at',
        'acknowledged_at',
        'resolved_at',
        'closed_at',
        'resolution_note',
    ];

    protected function casts(): array
    {
        return [
            'sla_due_at' => 'datetime',
            'acknowledged_at' => 'datetime',
            'resolved_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    public function reporter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function pic(): BelongsTo
    {
        return $this->belongsTo(User::class, 'pic_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(TicketStatusHistory::class)->orderBy('created_at');
    }

    public function assignments(): HasMany
    {
        return $this->hasMany(TicketAssignment::class)->orderBy('created_at');
    }

    public function whatsAppMessages(): HasMany
    {
        return $this->hasMany(WhatsAppMessage::class)->orderBy('created_at');
    }

    public function troubleshootingSessions(): HasMany
    {
        return $this->hasMany(TroubleshootingSession::class)->orderByDesc('created_at');
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class)->orderByDesc('created_at');
    }

    public function progressUpdates(): HasMany
    {
        return $this->hasMany(TicketProgressUpdate::class)->with(['user', 'attachments'])->orderBy('created_at');
    }

    public function timeline()
    {
        $statusHistories = $this->statusHistories
            ->map(fn ($h) => [
                'type' => 'STATUS_CHANGE',
                'at' => $h->created_at,
                'user' => $h->changedBy,
                'from_status' => $h->from_status,
                'to_status' => $h->to_status,
                'note' => $h->note,
            ]);

        $assignments = $this->assignments->map(fn ($a) => [
            'type' => 'ASSIGNED',
            'at' => $a->created_at,
            'user' => $a->assignedBy,
            'assigned_to' => $a->assignedTo,
            'assigned_team' => $a->assignedTeam,
            'note' => $a->note,
        ]);

        $progressUpdates = $this->progressUpdates->map(fn ($p) => [
            'type' => 'PROGRESS_UPDATE',
            'id' => $p->id,
            'at' => $p->created_at,
            'user' => $p->user,
            'title' => $p->title,
            'note' => $p->note,
            'status' => $p->status,
            'attachments' => $p->attachments,
        ]);

        return $statusHistories
            ->concat($assignments)
            ->concat($progressUpdates)
            ->sortBy('at')
            ->values();
    }

    public function isSlaBreached(): bool
    {
        if (! $this->sla_due_at) {
            return false;
        }

        $comparedAt = $this->resolved_at ?? $this->closed_at ?? now();

        return $comparedAt->greaterThan($this->sla_due_at);
    }

    public function slaStatus(): string
    {
        if (! $this->sla_due_at) {
            return 'N/A';
        }

        if (in_array($this->status, self::CLOSED_STATUSES, true)) {
            return $this->isSlaBreached() ? 'BREACHED' : 'OK';
        }

        if ($this->isSlaBreached()) {
            return 'BREACHED';
        }

        $warnAt = $this->created_at->copy()->addMinutes((int) ($this->sla_response_minutes * 0.8));

        return now()->greaterThanOrEqualTo($warnAt) ? 'WARNING' : 'OK';
    }

    /**
     * Normalize customer_phone to WhatsApp's expected format (62xxxxxxxxxx, no leading 0/+).
     */
    public function whatsappPhoneNumber(): ?string
    {
        if (blank($this->customer_phone)) {
            return null;
        }

        $digits = preg_replace('/\D/', '', $this->customer_phone);

        if (str_starts_with($digits, '0')) {
            $digits = '62'.substr($digits, 1);
        } elseif (! str_starts_with($digits, '62')) {
            $digits = '62'.$digits;
        }

        return $digits;
    }

    /**
     * Public, unauthenticated link for the customer to view repair progress, photos, and RFO.
     * Permanent link that does NOT expire, allowing clients to monitor progress and view solved tickets & RFO.
     */
    public function publicUrl(): ?string
    {
        if (blank($this->public_token)) {
            return null;
        }

        return url("/p/{$this->public_token}");
    }

    /**
     * Public link expiry check.
     * Returns false so clients can always view progress, resolved status, and RFO.
     */
    public function isPublicLinkExpired(): bool
    {
        return false;
    }

    /**
     * Calculate outage / downtime duration when ticket is resolved or closed.
     */
    public function outageDuration(): ?string
    {
        $endTime = $this->resolved_at ?? $this->closed_at;
        if (! $endTime || ! $this->created_at) {
            return null;
        }

        $totalMinutes = (int) $this->created_at->diffInMinutes($endTime);
        $hours = intdiv($totalMinutes, 60);
        $mins = $totalMinutes % 60;

        if ($hours > 0 && $mins > 0) {
            return "{$hours} Jam {$mins} Menit";
        } elseif ($hours > 0) {
            return "{$hours} Jam";
        } else {
            return max(1, $mins).' Menit';
        }
    }

    public function whatsappSummary(): string
    {
        $lines = [
            '*PT REPLAY INTI MEDIA*',
            "*Update Ticket: {$this->ticket_number}*",
            '',
            "Judul: {$this->title}",
            "Status: {$this->status}",
            "Priority: {$this->priority}",
        ];

        if ($this->customer_name) {
            $lines[] = "Pelanggan: {$this->customer_name}";
        }

        $lines[] = '';
        $lines[] = '*Timeline:*';

        foreach ($this->timeline() as $event) {
            $time = $event['at']->format('d/m H:i');

            if ($event['type'] === 'STATUS_CHANGE') {
                $desc = $event['from_status']
                    ? "Status {$event['from_status']} -> {$event['to_status']}"
                    : "Ticket dibuat, status {$event['to_status']}";
            } elseif ($event['type'] === 'PROGRESS_UPDATE') {
                $desc = 'Update Progres: '.($event['title'] ?: 'Tindakan Teknisi');
            } else {
                $desc = 'Ticket di-assign ke '.($event['assigned_to']?->name ?? '-');
            }

            $lines[] = "{$time} - {$desc}".($event['note'] ? " ({$event['note']})" : '');
        }

        if (in_array($this->status, self::CLOSED_STATUSES, true) && $this->resolution_note) {
            $lines[] = '';
            $lines[] = '*RFO (Reason For Outage):*';
            $lines[] = $this->resolution_note;
            if ($this->outageDuration()) {
                $lines[] = "Durasi Gangguan: {$this->outageDuration()}";
            }
        }

        if ($this->publicUrl()) {
            $lines[] = '';
            if (in_array($this->status, self::CLOSED_STATUSES, true)) {
                $lines[] = 'Lihat laporan tuntas & RFO: '.$this->publicUrl();
            } else {
                $lines[] = 'Pantau live progress perbaikan: '.$this->publicUrl();
            }
        }

        $lines[] = '';
        $lines[] = '— Tim NOC PT Replay Inti Media';

        return implode("\n", $lines);
    }

    public function getCustomerCategoryAttribute(): string
    {
        if ($this->customer?->category) {
            return strtoupper($this->customer->category);
        }

        if (str_starts_with($this->ticket_number, self::PREFIX_CORPORATE)) {
            return 'CORPORATE';
        }

        if (str_starts_with($this->ticket_number, self::PREFIX_RESELLER)) {
            return 'RESELLER';
        }

        return 'HOME';
    }

    public function scopeForCustomerCategory($query, string $category)
    {
        $cat = strtoupper($category);
        if ($cat === 'CORPORATE') {
            return $query->where(function ($sub) {
                $sub->whereHas('customer', fn ($c) => $c->where('category', 'CORPORATE'))
                    ->orWhere('ticket_number', 'like', self::PREFIX_CORPORATE.'%');
            });
        }

        if ($cat === 'RESELLER') {
            return $query->where(function ($sub) {
                $sub->whereHas('customer', fn ($c) => $c->where('category', 'RESELLER'))
                    ->orWhere('ticket_number', 'like', self::PREFIX_RESELLER.'%');
            });
        }

        if ($cat === 'HOME') {
            return $query->where(function ($sub) {
                $sub->whereHas('customer', fn ($c) => $c->where('category', 'HOME'))
                    ->orWhere('ticket_number', 'like', self::PREFIX_HOME.'%')
                    ->orWhere('ticket_number', 'like', 'PLG-%')
                    ->orWhere('ticket_number', 'like', 'INC-%')
                    ->orWhere(function ($s) {
                        $s->whereDoesntHave('customer')
                            ->where('ticket_number', 'not like', self::PREFIX_CORPORATE.'%')
                            ->where('ticket_number', 'not like', self::PREFIX_RESELLER.'%');
                    });
            });
        }

        return $query;
    }
}

