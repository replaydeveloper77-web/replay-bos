<?php

namespace App\Policies;

use App\Models\User;

class WorkOrderPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('workorder.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('workorder.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('workorder.manage');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('workorder.manage');
    }
}
