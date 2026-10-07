<?php

namespace App\Policies;

use App\Models\User;

class FieldVisitReportPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('fieldvisit.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('fieldvisit.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('fieldvisit.manage');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('fieldvisit.manage');
    }
}
