<?php

namespace App\Policies;

use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('invoice.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('invoice.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('invoice.create');
    }

    public function manage(User $user): bool
    {
        return $user->hasPermission('invoice.manage');
    }
}
