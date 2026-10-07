<?php

namespace App\Policies;

use App\Models\User;

class TeamPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('team.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('team.manage');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('team.manage');
    }
}
