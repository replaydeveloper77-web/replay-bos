<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;

class RoleService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function updatePermissions(Role $role, array $permissionIds, User $actor, Request $request): Role
    {
        $old = $role->permissions->pluck('slug')->all();

        $role->permissions()->sync($permissionIds);
        $role->load('permissions');

        $new = $role->permissions->pluck('slug')->all();

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ROLE',
            action: 'UPDATE_ROLE_PERMISSIONS',
            target: 'Role',
            targetId: $role->id,
            oldValue: ['permissions' => $old],
            newValue: ['permissions' => $new],
        );

        return $role;
    }
}
