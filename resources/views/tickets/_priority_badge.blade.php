@php
    $priorityClass = match ($ticket->priority) {
        'CRITICAL' => 'bg-danger',
        'HIGH' => 'bg-warning text-dark',
        'MEDIUM' => 'bg-info text-dark',
        default => 'bg-secondary',
    };
@endphp
<span class="badge {{ $priorityClass }}">{{ $ticket->priority }}</span>
