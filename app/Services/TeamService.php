<?php

namespace App\Services;

use App\Models\Team;
use App\Models\User;
use Illuminate\Http\Request;

class TeamService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function create(array $data, User $actor, Request $request): Team
    {
        $team = Team::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'is_active' => true,
            'created_by' => $actor->id,
        ]);

        $team->members()->sync($data['member_ids'] ?? []);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'TEAM',
            action: 'CREATE_TEAM',
            target: 'Team',
            targetId: $team->id,
            newValue: ['name' => $team->name, 'member_ids' => $data['member_ids'] ?? []],
        );

        return $team;
    }

    public function update(Team $team, array $data, User $actor, Request $request): Team
    {
        $old = $team->only(['name', 'description']) + ['member_ids' => $team->members->pluck('id')->all()];

        $team->update([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
        ]);

        $team->members()->sync($data['member_ids'] ?? []);
        $team->load('members');

        $new = $team->only(['name', 'description']) + ['member_ids' => $team->members->pluck('id')->all()];

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'TEAM',
            action: 'UPDATE_TEAM',
            target: 'Team',
            targetId: $team->id,
            oldValue: $old,
            newValue: $new,
        );

        return $team;
    }

    public function setActive(Team $team, bool $active, User $actor, Request $request): Team
    {
        $team->update(['is_active' => $active]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'TEAM',
            action: $active ? 'ENABLE_TEAM' : 'DISABLE_TEAM',
            target: 'Team',
            targetId: $team->id,
            newValue: ['is_active' => $active],
        );

        return $team;
    }
}
