<?php

namespace App\Policies;

use App\Models\User;

class LeaveRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('leave.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('leave.view');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('leave.manage');
    }
}
