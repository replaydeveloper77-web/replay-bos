<?php

namespace App\Policies;

use App\Models\Ticket;
use App\Models\User;

class TicketPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('ticket.view');
    }

    public function view(User $user, ?Ticket $ticket = null): bool
    {
        return $user->hasPermission('ticket.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('ticket.create');
    }

    public function update(User $user, ?Ticket $ticket = null): bool
    {
        return $user->hasPermission('ticket.update');
    }

    public function assign(User $user, ?Ticket $ticket = null): bool
    {
        return $user->hasPermission('ticket.assign');
    }

    public function escalate(User $user, ?Ticket $ticket = null): bool
    {
        return $user->hasPermission('ticket.escalate');
    }

    public function close(User $user, ?Ticket $ticket = null): bool
    {
        return $user->hasPermission('ticket.close');
    }

    public function delete(User $user, ?Ticket $ticket = null): bool
    {
        return $user->hasPermission('ticket.update') || $user->hasRole('super-admin');
    }
}
