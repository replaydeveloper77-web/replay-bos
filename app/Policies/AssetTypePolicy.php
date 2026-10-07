<?php

namespace App\Policies;

use App\Models\User;

class AssetTypePolicy
{
    public function viewAny(User $user): bool
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
