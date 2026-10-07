<?php

namespace App\Services;

use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Http\Request;

class ServicePackageService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function create(array $data, User $actor, Request $request): ServicePackage
    {
        $package = ServicePackage::create([
            'name' => $data['name'],
            'speed_label' => $data['speed_label'] ?? null,
            'price' => $data['price'],
            'description' => $data['description'] ?? null,
            'is_active' => true,
            'created_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'PACKAGE',
            action: 'CREATE_PACKAGE',
            target: 'ServicePackage',
            targetId: $package->id,
            newValue: $package->only(['name', 'speed_label', 'price']),
        );

        return $package;
    }

    public function update(ServicePackage $package, array $data, User $actor, Request $request): ServicePackage
    {
        $old = $package->only(['name', 'speed_label', 'price', 'description']);

        $package->update($data);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'PACKAGE',
            action: 'UPDATE_PACKAGE',
            target: 'ServicePackage',
            targetId: $package->id,
            oldValue: $old,
            newValue: $package->only(['name', 'speed_label', 'price', 'description']),
        );

        return $package;
    }

    public function setActive(ServicePackage $package, bool $active, User $actor, Request $request): ServicePackage
    {
        $package->update(['is_active' => $active]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'PACKAGE',
            action: $active ? 'ENABLE_PACKAGE' : 'DISABLE_PACKAGE',
            target: 'ServicePackage',
            targetId: $package->id,
            newValue: ['is_active' => $active],
        );

        return $package;
    }
}
