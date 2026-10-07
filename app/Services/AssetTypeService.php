<?php

namespace App\Services;

use App\Models\AssetType;
use App\Models\User;
use Illuminate\Http\Request;

class AssetTypeService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function create(array $data, User $actor, Request $request): AssetType
    {
        $assetType = AssetType::create([
            'name' => $data['name'],
            'category' => $data['category'],
            'unit' => $data['unit'] ?? 'pcs',
            'notes' => $data['notes'] ?? null,
            'is_active' => true,
            'created_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ASSET_TYPE',
            action: 'CREATE_ASSET_TYPE',
            target: 'AssetType',
            targetId: $assetType->id,
            newValue: $assetType->only(['name', 'category', 'unit']),
        );

        return $assetType;
    }

    public function update(AssetType $assetType, array $data, User $actor, Request $request): AssetType
    {
        $old = $assetType->only(['name', 'category', 'unit', 'notes']);

        $assetType->update($data);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ASSET_TYPE',
            action: 'UPDATE_ASSET_TYPE',
            target: 'AssetType',
            targetId: $assetType->id,
            oldValue: $old,
            newValue: $assetType->only(['name', 'category', 'unit', 'notes']),
        );

        return $assetType;
    }

    public function setActive(AssetType $assetType, bool $active, User $actor, Request $request): AssetType
    {
        $assetType->update(['is_active' => $active]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ASSET_TYPE',
            action: $active ? 'ENABLE_ASSET_TYPE' : 'DISABLE_ASSET_TYPE',
            target: 'AssetType',
            targetId: $assetType->id,
            newValue: ['is_active' => $active],
        );

        return $assetType;
    }
}
