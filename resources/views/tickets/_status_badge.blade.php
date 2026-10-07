@php
    $statusClass = match ($ticket->status) {
        'OPEN' => 'bg-primary',
        'ACKNOWLEDGED' => 'bg-info text-dark',
        'INVESTIGATING' => 'bg-warning text-dark',
        'HANDLE' => 'bg-primary text-white',
        'WAITING' => 'bg-secondary',
        'ESCALATED' => 'bg-danger',
        'MONITORING' => 'bg-info text-dark',
        'RESOLVED' => 'bg-success',
        'CLOSED' => 'bg-dark',
        default => 'bg-secondary',
    };
@endphp
<span class="badge {{ $statusClass }}">{{ $ticket->status }}</span>
