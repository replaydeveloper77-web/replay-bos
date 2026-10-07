<?php

namespace App\Policies;

use App\Models\User;

class CoveragePointPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('coverage.manage');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('coverage.manage');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('coverage.manage');
    }
}
