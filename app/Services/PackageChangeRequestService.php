<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\PackageChangeRequest;
use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class PackageChangeRequestService
{
    public function __construct(
        private AuditService $auditService,
        private WorkOrderService $workOrderService
    ) {
    }

    public function submit(array $data, User $actor, Request $request): PackageChangeRequest
    {
        return DB::transaction(function () use ($data, $actor, $request) {
            $customer = Customer::findOrFail($data['customer_id']);
            $targetPackageName = trim($data['target_package'] ?? ($data['target_package_name'] ?? ''));

            // Check if matched service package exists in catalog
            $targetPackageId = $data['target_service_package_id'] ?? null;
            if (! $targetPackageId && $targetPackageName) {
                $matched = ServicePackage::where('name', $targetPackageName)->first();
                if ($matched) {
                    $targetPackageId = $matched->id;
                }
            }
            $targetPackage = $targetPackageId ? ServicePackage::find($targetPackageId) : null;

            $type = $data['type'] ?? 'UPGRADE';
            $requestNumber = $this->generateRequestNumber($type);

            $currentPackageId = $customer->service_package_id;
            $currentPackageName = $customer->servicePackage?->name ?: ($customer->service ?: null);
            $currentPrice = $customer->subscription_price;

            $targetPrice = isset($data['target_price']) && $data['target_price'] !== '' && $data['target_price'] !== null
                ? (float) $data['target_price']
                : ($targetPackage ? (float) $targetPackage->price : null);

            $changeRequest = PackageChangeRequest::create([
                'request_number' => $requestNumber,
                'customer_id' => $customer->id,
                'type' => $type,
                'current_service_package_id' => $currentPackageId,
                'current_package_name' => $currentPackageName,
                'current_price' => $currentPrice,
                'target_service_package_id' => $targetPackageId,
                'target_package_name' => $targetPackageName,
                'target_price' => $targetPrice,
                'notes' => $data['notes'] ?? null,
                'status' => 'PENDING',
                'requested_by' => $actor->id,
            ]);

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'PACKAGE_CHANGE_REQUEST',
                action: 'SUBMIT_PACKAGE_CHANGE_REQUEST',
                target: 'PackageChangeRequest',
                targetId: $changeRequest->id,
                newValue: $changeRequest->only([
                    'request_number',
                    'customer_id',
                    'type',
                    'current_service_package_id',
                    'current_package_name',
                    'target_service_package_id',
                    'target_package_name',
                    'target_price',
                    'status',
                ]),
            );

            return $changeRequest;
        });
    }

    public function approve(PackageChangeRequest $changeRequest, ?string $financeNotes, User $actor, Request $request, ?float $newPrice = null): PackageChangeRequest
    {
        if (! $changeRequest->isPending()) {
            throw new RuntimeException('Hanya pengajuan dengan status Menunggu Finance yang dapat disetujui.');
        }

        return DB::transaction(function () use ($changeRequest, $financeNotes, $actor, $request, $newPrice) {
            // Optional update of target_price if Finance entered it upon approval
            if ($newPrice !== null && $newPrice >= 0) {
                $changeRequest->target_price = $newPrice;
            }

            // 1. Generate Work Order secara otomatis
            $workOrder = $this->workOrderService->createFromPackageChange($changeRequest, $actor, $financeNotes);

            // 2. Perbarui data paket pelanggan
            $targetPackage = $changeRequest->targetPackage;
            $customer = $changeRequest->customer;
            $customerUpdate = [
                'service' => $changeRequest->target_package_name ?: ($targetPackage?->name ?? 'Paket Baru'),
            ];
            if ($changeRequest->target_service_package_id) {
                $customerUpdate['service_package_id'] = $changeRequest->target_service_package_id;
            }
            if ($changeRequest->target_price !== null) {
                $customerUpdate['subscription_price'] = $changeRequest->target_price;
            }
            $customer->update($customerUpdate);

            // 3. Perbarui status permohonan
            $changeRequest->update([
                'status' => 'APPROVED',
                'target_price' => $changeRequest->target_price,
                'approved_by' => $actor->id,
                'approved_at' => now(),
                'finance_notes' => $financeNotes,
                'work_order_id' => $workOrder->id,
            ]);

            // 4. Catat Audit Log
            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'PACKAGE_CHANGE_REQUEST',
                action: 'APPROVE_PACKAGE_CHANGE_REQUEST',
                target: 'PackageChangeRequest',
                targetId: $changeRequest->id,
                newValue: [
                    'status' => 'APPROVED',
                    'work_order_id' => $workOrder->id,
                    'work_order_number' => $workOrder->work_order_number,
                    'finance_notes' => $financeNotes,
                ],
            );

            return $changeRequest;
        });
    }

    public function reject(PackageChangeRequest $changeRequest, string $financeNotes, User $actor, Request $request): PackageChangeRequest
    {
        if (! $changeRequest->isPending()) {
            throw new RuntimeException('Hanya pengajuan dengan status Menunggu Finance yang dapat ditolak.');
        }

        return DB::transaction(function () use ($changeRequest, $financeNotes, $actor, $request) {
            $changeRequest->update([
                'status' => 'REJECTED',
                'approved_by' => $actor->id,
                'approved_at' => now(),
                'finance_notes' => $financeNotes,
            ]);

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'PACKAGE_CHANGE_REQUEST',
                action: 'REJECT_PACKAGE_CHANGE_REQUEST',
                target: 'PackageChangeRequest',
                targetId: $changeRequest->id,
                newValue: [
                    'status' => 'REJECTED',
                    'finance_notes' => $financeNotes,
                ],
            );

            return $changeRequest;
        });
    }

    public function cancel(PackageChangeRequest $changeRequest, User $actor, Request $request): PackageChangeRequest
    {
        if (! $changeRequest->isPending()) {
            throw new RuntimeException('Hanya pengajuan dengan status Menunggu Finance yang dapat dibatalkan.');
        }

        return DB::transaction(function () use ($changeRequest, $actor, $request) {
            $changeRequest->update([
                'status' => 'CANCELLED',
            ]);

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'PACKAGE_CHANGE_REQUEST',
                action: 'CANCEL_PACKAGE_CHANGE_REQUEST',
                target: 'PackageChangeRequest',
                targetId: $changeRequest->id,
                newValue: ['status' => 'CANCELLED'],
            );

            return $changeRequest;
        });
    }

    public function generateRequestNumber(string $type = 'UPGRADE'): string
    {
        return DB::transaction(function () use ($type) {
            $prefix = $type === 'DOWNGRADE' ? 'REQ-DWN-' : 'REQ-UPG-';

            $lastNumber = PackageChangeRequest::where('request_number', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('request_number')
                ->value('request_number');

            $nextSeq = $lastNumber ? ((int) substr($lastNumber, strlen($prefix))) + 1 : 1;

            return $prefix . str_pad((string) $nextSeq, 6, '0', STR_PAD_LEFT);
        });
    }
}
