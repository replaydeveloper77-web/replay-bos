<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AssetService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function create(array $data, User $actor, Request $request): Asset
    {
        $asset = Asset::create([
            'asset_code' => $this->generateAssetCode(),
            'asset_type_id' => $data['asset_type_id'],
            'serial_number' => $data['serial_number'] ?? null,
            'status' => 'IN_STOCK',
            'purchase_date' => $data['purchase_date'] ?? null,
            'price' => $data['price'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ASSET',
            action: 'CREATE_ASSET',
            target: 'Asset',
            targetId: $asset->id,
            newValue: $asset->only(['asset_code', 'serial_number', 'asset_type_id']),
        );

        return $asset;
    }

    public function update(Asset $asset, array $data, User $actor, Request $request): Asset
    {
        $old = $asset->only(['serial_number', 'purchase_date', 'price', 'notes']);

        $asset->update([
            'serial_number' => $data['serial_number'] ?? null,
            'purchase_date' => $data['purchase_date'] ?? null,
            'price' => $data['price'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ASSET',
            action: 'UPDATE_ASSET',
            target: 'Asset',
            targetId: $asset->id,
            oldValue: $old,
            newValue: $asset->only(['serial_number', 'purchase_date', 'price', 'notes']),
        );

        return $asset;
    }

    /**
     * Moves an asset between statuses — the only place customer_id/coverage_point_id
     * ever change, so the "where is this thing right now" story stays consistent and
     * every transition lands in the audit trail (who moved it, from where, to where).
     */
    public function changeStatus(Asset $asset, string $status, array $data, User $actor, Request $request): Asset
    {
        if (! in_array($status, Asset::STATUSES, true)) {
            throw new RuntimeException('Status tidak valid.');
        }

        if ($status === 'INSTALLED' && empty($data['customer_id']) && empty($data['coverage_point_id'])) {
            throw new RuntimeException('Pilih pelanggan atau titik coverage tujuan pemasangan dulu.');
        }

        $old = $asset->only(['status', 'customer_id', 'coverage_point_id', 'location_note']);

        $asset->update([
            'status' => $status,
            'customer_id' => $status === 'INSTALLED' ? ($data['customer_id'] ?? null) : null,
            'coverage_point_id' => $status === 'INSTALLED' ? ($data['coverage_point_id'] ?? null) : null,
            'location_note' => $data['location_note'] ?? null,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'ASSET',
            action: 'CHANGE_ASSET_STATUS',
            target: 'Asset',
            targetId: $asset->id,
            oldValue: $old,
            newValue: $asset->only(['status', 'customer_id', 'coverage_point_id', 'location_note']),
        );

        return $asset;
    }

    private function generateAssetCode(): string
    {
        return DB::transaction(function () {
            $prefix = 'AST-';

            $lastNumber = Asset::where('asset_code', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('asset_code')
                ->value('asset_code');

            $nextSeq = $lastNumber ? ((int) substr($lastNumber, strlen($prefix))) + 1 : 1;

            return $prefix.str_pad((string) $nextSeq, 6, '0', STR_PAD_LEFT);
        });
    }
}
