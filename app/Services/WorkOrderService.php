<?php

namespace App\Services;

use App\Models\User;
use App\Models\WorkOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class WorkOrderService
{
    public function __construct(private AuditService $auditService, private CustomerService $customerService)
    {
    }

    public function create(array $data, User $actor, Request $request): WorkOrder
    {
        $workOrder = WorkOrder::create([
            'work_order_number' => $this->generateWorkOrderNumber(),
            'customer_id' => $data['customer_id'],
            'type' => $data['type'] ?? 'INSTALASI_BARU',
            'status' => 'SCHEDULED',
            'assigned_to_id' => $data['assigned_to_id'] ?? null,
            'team_id' => $data['team_id'] ?? null,
            'scheduled_date' => $data['scheduled_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'WORK_ORDER',
            action: 'CREATE_WORK_ORDER',
            target: 'WorkOrder',
            targetId: $workOrder->id,
            newValue: $workOrder->only(['work_order_number', 'customer_id', 'type', 'assigned_to_id', 'team_id', 'scheduled_date']),
        );

        return $workOrder;
    }

    public function update(WorkOrder $workOrder, array $data, User $actor, Request $request): WorkOrder
    {
        $old = $workOrder->only(['assigned_to_id', 'team_id', 'scheduled_date', 'notes']);

        $workOrder->update([
            'assigned_to_id' => $data['assigned_to_id'] ?? null,
            'team_id' => $data['team_id'] ?? null,
            'scheduled_date' => $data['scheduled_date'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'WORK_ORDER',
            action: 'UPDATE_WORK_ORDER',
            target: 'WorkOrder',
            targetId: $workOrder->id,
            oldValue: $old,
            newValue: $workOrder->only(['assigned_to_id', 'team_id', 'scheduled_date', 'notes']),
        );

        return $workOrder;
    }

    /**
     * Moves a work order through its lifecycle. Completing an INSTALASI_BARU order
     * against a customer still pending activation flips that customer straight to
     * ACTIVE — the same transition the manual "Tandai Aktivasi Selesai" button does —
     * so a technician closing out an install doesn't leave a second manual step behind.
     */
    public function changeStatus(WorkOrder $workOrder, string $status, array $data, User $actor, Request $request): WorkOrder
    {
        if (! in_array($status, WorkOrder::STATUSES, true)) {
            throw new RuntimeException('Status tidak valid.');
        }

        if ($status === 'DONE' && empty($data['completion_notes']) && empty($workOrder->completion_notes)) {
            throw new RuntimeException('Isi catatan hasil pengerjaan dulu sebelum menandai selesai.');
        }

        $old = $workOrder->only(['status', 'completion_notes']);

        $workOrder->update([
            'status' => $status,
            'completion_notes' => $data['completion_notes'] ?? $workOrder->completion_notes,
            'completed_at' => $status === 'DONE' ? now() : null,
            'completed_by' => $status === 'DONE' ? $actor->id : null,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'WORK_ORDER',
            action: 'CHANGE_WORK_ORDER_STATUS',
            target: 'WorkOrder',
            targetId: $workOrder->id,
            oldValue: $old,
            newValue: $workOrder->only(['status', 'completion_notes']),
        );

        if ($status === 'DONE') {
            $customer = $workOrder->customer;
            if ($customer && $customer->isPendingActivation()) {
                $this->customerService->markActivated($customer, $actor, $request);
            }
        }

        return $workOrder;
    }

    public function createFromPackageChange(\App\Models\PackageChangeRequest $changeRequest, User $actor, ?string $notes = null): WorkOrder
    {
        $changeRequest->loadMissing('customer', 'currentPackage', 'targetPackage');
        $fromName = $changeRequest->currentPackageLabel();
        $toName = $changeRequest->targetPackageLabel();

        $woNotes = "Work Order {$changeRequest->typeLabel()} Paket: {$fromName} -> {$toName}. [Ref: {$changeRequest->request_number}]";
        if ($notes) {
            $woNotes .= "\nCatatan Finance: {$notes}";
        }
        if ($changeRequest->notes) {
            $woNotes .= "\nCatatan Pengajuan: {$changeRequest->notes}";
        }

        $workOrder = WorkOrder::create([
            'work_order_number' => $this->generateWorkOrderNumber(),
            'customer_id' => $changeRequest->customer_id,
            'type' => $changeRequest->type,
            'status' => 'SCHEDULED',
            'scheduled_date' => now()->toDateString(),
            'notes' => $woNotes,
            'created_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: request(),
            module: 'WORK_ORDER',
            action: 'CREATE_WORK_ORDER',
            target: 'WorkOrder',
            targetId: $workOrder->id,
            newValue: $workOrder->only(['work_order_number', 'customer_id', 'type', 'scheduled_date']),
        );

        return $workOrder;
    }

    public function generateWorkOrderNumber(): string
    {
        return DB::transaction(function () {
            $prefix = 'WO-';

            $lastNumber = WorkOrder::where('work_order_number', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByDesc('work_order_number')
                ->value('work_order_number');

            $nextSeq = $lastNumber ? ((int) substr($lastNumber, strlen($prefix))) + 1 : 1;

            return $prefix.str_pad((string) $nextSeq, 6, '0', STR_PAD_LEFT);
        });
    }
}
