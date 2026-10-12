<?php

namespace App\Policies;

use App\Models\User;

class RolePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('role.view') || $user->hasRole('super-admin');
    }

    public function manage(User $user): bool
    {
        return $user->hasPermission('role.manage') || $user->hasRole('super-admin');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super-admin') || $user->hasPermission('role.manage');
    }

    public function createPermission(User $user): bool
    {
        return $user->hasRole('super-admin') || $user->hasPermission('role.manage');
    }
}

