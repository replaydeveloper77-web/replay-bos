<?php

namespace App\Policies;

use App\Models\PackageChangeRequest;
use App\Models\User;

class PackageChangeRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasRole('super-admin')
            || $user->hasRole('finance')
            || $user->hasPermission('package.change.view');
    }

    public function view(User $user): bool
    {
        return $user->hasRole('super-admin')
            || $user->hasRole('finance')
            || $user->hasPermission('package.change.view');
    }

    public function create(User $user): bool
    {
        return $user->hasRole('super-admin')
            || $user->hasPermission('package.change.create');
    }

    public function approve(User $user): bool
    {
        return $user->hasRole('super-admin')
            || $user->hasRole('finance')
            || $user->hasPermission('package.change.approve');
    }

    public function reject(User $user): bool
    {
        return $this->approve($user);
    }

    public function cancel(User $user, PackageChangeRequest $changeRequest): bool
    {
        return $user->hasRole('super-admin')
            || $user->id === $changeRequest->requested_by;
    }
}
