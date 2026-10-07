<?php

namespace App\Services;

use App\Models\CoveragePoint;
use App\Models\User;
use Illuminate\Http\Request;

class CoveragePointService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function create(array $data, User $actor, Request $request): CoveragePoint
    {
        $point = CoveragePoint::create([
            'name' => $data['name'],
            'latitude' => $data['latitude'],
            'longitude' => $data['longitude'],
            'radius_km' => $data['radius_km'],
            'notes' => $data['notes'] ?? null,
            'is_active' => true,
            'created_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'COVERAGE',
            action: 'CREATE_COVERAGE_POINT',
            target: 'CoveragePoint',
            targetId: $point->id,
            newValue: $point->only(['name', 'latitude', 'longitude', 'radius_km']),
        );

        return $point;
    }

    public function update(CoveragePoint $point, array $data, User $actor, Request $request): CoveragePoint
    {
        $old = $point->only(['name', 'latitude', 'longitude', 'radius_km', 'notes']);

        $point->update($data);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'COVERAGE',
            action: 'UPDATE_COVERAGE_POINT',
            target: 'CoveragePoint',
            targetId: $point->id,
            oldValue: $old,
            newValue: $point->only(['name', 'latitude', 'longitude', 'radius_km', 'notes']),
        );

        return $point;
    }

    public function setActive(CoveragePoint $point, bool $active, User $actor, Request $request): CoveragePoint
    {
        $point->update(['is_active' => $active]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'COVERAGE',
            action: $active ? 'ENABLE_COVERAGE_POINT' : 'DISABLE_COVERAGE_POINT',
            target: 'CoveragePoint',
            targetId: $point->id,
            newValue: ['is_active' => $active],
        );

        return $point;
    }
}
