<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('user.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('user.create');
    }

    public function update(User $user, User $target): bool
    {
        return $user->hasPermission('user.update');
    }

    public function disable(User $user, User $target): bool
    {
        return $user->hasPermission('user.disable') && $user->id !== $target->id;
    }
}
