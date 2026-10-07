<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function create(array $data, User $actor, Request $request): User
    {
        $user = User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
            'customer_id' => $data['customer_id'] ?? null,
        ]);

        $user->roles()->sync($data['role_ids'] ?? []);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'USER',
            action: 'CREATE_USER',
            target: 'User',
            targetId: $user->id,
            newValue: $user->only(['name', 'username', 'email']) + ['role_ids' => $data['role_ids'] ?? []],
        );

        return $user;
    }

    public function update(User $user, array $data, User $actor, Request $request): User
    {
        $old = $user->only(['name', 'username', 'email']) + ['role_ids' => $user->roles->pluck('id')->all()];

        $user->fill([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['email'],
            'customer_id' => $data['customer_id'] ?? null,
        ]);

        if (! empty($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();
        $user->roles()->sync($data['role_ids'] ?? []);

        $new = $user->only(['name', 'username', 'email']) + ['role_ids' => $data['role_ids'] ?? []];

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'USER',
            action: 'UPDATE_USER',
            target: 'User',
            targetId: $user->id,
            oldValue: $old,
            newValue: $new,
        );

        return $user;
    }

    public function setActive(User $user, bool $active, User $actor, Request $request): User
    {
        $old = ['is_active' => $user->is_active];
        $user->update(['is_active' => $active]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'USER',
            action: $active ? 'ENABLE_USER' : 'DISABLE_USER',
            target: 'User',
            targetId: $user->id,
            oldValue: $old,
            newValue: ['is_active' => $active],
        );

        return $user;
    }
}
