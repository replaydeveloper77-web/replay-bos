<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RoleService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function createRole(array $data, User $actor, Request $request): Role
    {
        $rawSlug = !empty($data['slug']) ? Str::slug($data['slug']) : Str::slug($data['name']);
        if (empty($rawSlug)) {
            $rawSlug = 'role-' . time();
        }

        $slug = $rawSlug;
        $count = 1;
        while (Role::where('slug', $slug)->exists()) {
            $slug = "{$rawSlug}-{$count}";
            $count++;
        }

        $role = Role::create([
            'name' => trim($data['name']),
            'slug' => $slug,
            'description' => !empty($data['description']) ? trim($data['description']) : null,
        ]);

        if (!empty($data['permission_ids'])) {
            $role->permissions()->sync($data['permission_ids']);
        }

        $role->load('permissions');

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ROLE',
            action: 'CREATE_ROLE',
            target: 'Role',
            targetId: $role->id,
            oldValue: null,
            newValue: [
                'name' => $role->name,
                'slug' => $role->slug,
                'description' => $role->description,
                'permissions' => $role->permissions->pluck('slug')->all(),
            ],
        );

        return $role;
    }

    public function createPermission(array $data, User $actor, Request $request): Permission
    {
        $slug = strtolower(trim($data['slug']));

        $permission = Permission::create([
            'name' => trim($data['name']),
            'slug' => $slug,
            'module' => strtolower(trim($data['module'])),
            'description' => !empty($data['description']) ? trim($data['description']) : null,
        ]);

        if (!empty($data['role_ids'])) {
            $permission->roles()->sync($data['role_ids']);
        }

        $permission->load('roles');

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ROLE',
            action: 'CREATE_PERMISSION',
            target: 'Permission',
            targetId: $permission->id,
            oldValue: null,
            newValue: [
                'name' => $permission->name,
                'slug' => $permission->slug,
                'module' => $permission->module,
                'roles' => $permission->roles->pluck('slug')->all(),
            ],
        );

        return $permission;
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

