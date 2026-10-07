<?php

namespace App\Policies;

use App\Models\User;

class AttendancePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('attendance.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('attendance.view');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('attendance.manage');
    }
}
