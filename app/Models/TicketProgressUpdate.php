<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TicketProgressUpdate extends Model
{
    protected $fillable = [
        'ticket_id',
        'user_id',
        'title',
        'note',
        'status',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(TicketAttachment::class, 'ticket_progress_update_id')->orderBy('created_at');
    }

    public function photos(): Collection
    {
        return $this->attachments->filter->isImage();
    }
}
