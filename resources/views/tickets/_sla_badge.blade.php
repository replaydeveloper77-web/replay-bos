@php
    $slaStatus = $ticket->slaStatus();
    $slaClass = match ($slaStatus) {
        'OK' => 'bg-success',
        'WARNING' => 'bg-warning text-dark',
        'BREACHED' => 'bg-danger',
        default => 'bg-secondary',
    };
@endphp
<span class="badge {{ $slaClass }}">{{ $slaStatus }}</span>
