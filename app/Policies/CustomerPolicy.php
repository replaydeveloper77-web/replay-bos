<?php

namespace App\Policies;

use App\Models\User;

class CustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('customer.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('customer.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('customer.manage') || $user->hasPermission('customer.create');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('customer.manage');
    }

    public function activate(User $user): bool
    {
        return $user->hasPermission('customer.manage') || $user->hasPermission('customer.activate');
    }

    public function delete(User $user, ?Customer $customer = null): bool
    {
        return $user->hasPermission('customer.manage');
    }
}
