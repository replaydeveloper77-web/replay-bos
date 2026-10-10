<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class TicketAttachment extends Model
{
    protected $fillable = [
        'ticket_id',
        'ticket_progress_update_id',
        'uploaded_by',
        'disk_path',
        'original_filename',
        'mime_type',
        'file_size',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }

    public function progressUpdate(): BelongsTo
    {
        return $this->belongsTo(TicketProgressUpdate::class, 'ticket_progress_update_id');
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function url(): string
    {
        return Storage::disk('public')->url($this->disk_path);
    }

    public function isImage(): bool
    {
        return str_starts_with($this->mime_type, 'image/');
    }
}
