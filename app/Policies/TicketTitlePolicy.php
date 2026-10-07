<?php

namespace App\Policies;

use App\Models\TicketTitle;
use App\Models\User;

class TicketTitlePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('ticket.view');
    }

    public function view(User $user, ?TicketTitle $ticketTitle = null): bool
    {
        return $user->hasPermission('ticket.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('ticket.create') || $user->hasRole('super-admin');
    }

    public function update(User $user, ?TicketTitle $ticketTitle = null): bool
    {
        return $user->hasPermission('ticket.update') || $user->hasRole('super-admin');
    }

    public function delete(User $user, ?TicketTitle $ticketTitle = null): bool
    {
        return $user->hasPermission('ticket.update') || $user->hasRole('super-admin');
    }
}
