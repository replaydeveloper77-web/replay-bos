<?php

namespace App\Policies;

use App\Models\User;

class AssetPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermission('asset.view');
    }

    public function view(User $user): bool
    {
        return $user->hasPermission('asset.view');
    }

    public function create(User $user): bool
    {
        return $user->hasPermission('asset.manage');
    }

    public function update(User $user): bool
    {
        return $user->hasPermission('asset.manage');
    }
}
