<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateRolePermissionsRequest;
use App\Models\Permission;
use App\Models\Role;
use App\Services\RoleService;

class RoleController extends Controller
{
    public function __construct(private RoleService $roleService)
    {
    }

    public function index()
    {
        $this->authorize('viewAny', Role::class);

        $roles = Role::withCount('users', 'permissions')->orderBy('name')->get();

        return view('roles.index', compact('roles'));
    }

    public function edit(Role $role)
    {
        $this->authorize('manage', Role::class);

        $role->load('permissions');
        $permissionsByModule = Permission::orderBy('name')->get()->groupBy('module');
        $assignedIds = $role->permissions->pluck('id')->all();

        return view('roles.edit', compact('role', 'permissionsByModule', 'assignedIds'));
    }

    public function update(UpdateRolePermissionsRequest $request, Role $role)
    {
        $this->roleService->updatePermissions($role, $request->validated()['permission_ids'] ?? [], $request->user(), $request);

        return redirect()->route('roles.index')->with('status', 'Permission role berhasil diperbarui.');
    }
}
