<?php

namespace App\Policies;

use App\Models\User;

class ServicePackagePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('package.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('package.manage');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('package.manage');
    }
}
