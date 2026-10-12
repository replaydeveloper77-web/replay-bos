<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\StoreRoleRequest;
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
        $permissionsByModule = Permission::with('roles')->orderBy('module')->orderBy('name')->get()->groupBy('module');
        $allPermissions = Permission::with('roles')->orderBy('module')->orderBy('name')->get();
        $modules = Permission::select('module')->distinct()->orderBy('module')->pluck('module')->all();

        return view('roles.index', compact('roles', 'permissionsByModule', 'allPermissions', 'modules'));
    }

    public function store(StoreRoleRequest $request)
    {
        $this->authorize('create', Role::class);

        $role = $this->roleService->createRole($request->validated(), $request->user(), $request);

        return redirect()->route('roles.index')->with('status', "Role '{$role->name}' berhasil ditambahkan.");
    }

    public function storePermission(StorePermissionRequest $request)
    {
        $this->authorize('createPermission', Role::class);

        $permission = $this->roleService->createPermission($request->validated(), $request->user(), $request);

        return redirect()->route('roles.index')->with('status', "Permission '{$permission->name}' ({$permission->slug}) berhasil ditambahkan.");
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

