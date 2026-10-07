<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhatsAppMessage extends Model
{
    protected $table = 'whatsapp_messages';

    public const RECIPIENT_TYPES = ['CUSTOMER', 'CUSTOM'];

    public const STATUSES = ['PENDING', 'QUEUED', 'SENT', 'DELIVERED', 'READ', 'FAILED'];

    protected $fillable = [
        'ticket_id',
        'recipient_type',
        'recipient_name',
        'phone',
        'template_key',
        'message',
        'sent_by',
        'sent_at',
        'status',
        'provider',
        'provider_message_id',
        'error_message',
    ];

    protected function casts(): array
    {
        return [
            'sent_at' => 'datetime',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function sentBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function maskedPhone(): string
    {
        $digits = $this->phone;
        if (strlen($digits) <= 6) {
            return $digits;
        }

        return substr($digits, 0, 4).str_repeat('*', strlen($digits) - 6).substr($digits, -2);
    }
}
