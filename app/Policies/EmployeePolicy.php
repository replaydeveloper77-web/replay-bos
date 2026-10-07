<?php

namespace App\Policies;

use App\Models\User;

class EmployeePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('employee.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('employee.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('employee.create') || $user->hasPermission('employee.manage');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('employee.manage');
    }
}
