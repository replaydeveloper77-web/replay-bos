<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CustomerService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function create(array $data, User $actor, Request $request): Customer
    {
        $category = $data['category'] ?? 'HOME';

        $customer = Customer::create([
            'name' => $data['name'],
            'category' => $category,
            'reseller_id' => $data['reseller_id'] ?? null,
            'company' => $data['company'] ?? null,
            'pic_name' => $data['pic_name'] ?? null,
            'whatsapp' => $data['whatsapp'] ?? null,
            'email' => $data['email'] ?? null,
            'location' => $data['location'] ?? null,
            'service' => $data['service'] ?? null,
            'service_number' => ($data['service_number'] ?? null) ?: $this->generateServiceNumber($category),
            'service_package_id' => $data['service_package_id'] ?? null,
            'subscription_price' => $data['subscription_price'] ?? null,
            'billing_due_day' => $data['billing_due_day'] ?? 10,
            'notes' => $data['notes'] ?? null,
            'is_active' => true,
            'activation_status' => $data['activation_status'] ?? 'ACTIVE',
            'created_by' => $actor->id,
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'CUSTOMER',
            action: 'CREATE_CUSTOMER',
            target: 'Customer',
            targetId: $customer->id,
            newValue: $customer->only(['name', 'company', 'whatsapp']),
        );

        return $customer;
    }

    public function update(Customer $customer, array $data, User $actor, Request $request): Customer
    {
        $old = $customer->only(['name', 'company', 'pic_name', 'whatsapp', 'email', 'location', 'service', 'service_number', 'notes']);

        $category = $data['category'] ?? $customer->category ?? 'HOME';
        if (empty($data['service_number'])) {
            $data['service_number'] = $this->generateServiceNumber($category);
        }

        $customer->update($data);

        $new = $customer->only(['name', 'company', 'pic_name', 'whatsapp', 'email', 'location', 'service', 'service_number', 'notes']);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'CUSTOMER',
            action: 'UPDATE_CUSTOMER',
            target: 'Customer',
            targetId: $customer->id,
            oldValue: $old,
            newValue: $new,
        );

        return $customer;
    }

    public function setActive(Customer $customer, bool $active, User $actor, Request $request): Customer
    {
        $customer->update(['is_active' => $active]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'CUSTOMER',
            action: $active ? 'ENABLE_CUSTOMER' : 'DISABLE_CUSTOMER',
            target: 'Customer',
            targetId: $customer->id,
            newValue: ['is_active' => $active],
        );

        return $customer;
    }

    public function markActivated(Customer $customer, User $actor, Request $request): Customer
    {
        $customer->update(['activation_status' => 'ACTIVE']);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'CUSTOMER',
            action: 'ACTIVATE_CUSTOMER',
            target: 'Customer',
            targetId: $customer->id,
            newValue: ['activation_status' => 'ACTIVE'],
        );

        return $customer;
    }

    public function delete(Customer $customer, User $actor, Request $request): void
    {
        DB::transaction(function () use ($customer, $actor, $request) {
            // Nullify reseller_id for any home customers under this reseller to prevent FK restrict error
            $customer->homeCustomers()->update(['reseller_id' => null]);

            // Clean up IP allocations
            $customer->ipAllocations()->delete();

            $snapshot = $customer->only(['id', 'name', 'company', 'service_number']);

            $customer->delete();

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'CUSTOMER',
                action: 'DELETE_CUSTOMER',
                target: 'Customer',
                targetId: $snapshot['id'],
                oldValue: $snapshot,
            );
        });
    }

    public function generateServiceNumber(string $category = 'HOME'): string
    {
        return DB::transaction(function () use ($category) {
            $prefix = match (strtoupper($category)) {
                'RESELLER' => Customer::SERVICE_PREFIX_RESELLER,
                'CORPORATE' => Customer::SERVICE_PREFIX_CORPORATE,
                default => Customer::SERVICE_PREFIX_HOME,
            };

            // Based on the highest number already issued with this prefix, not a row count —
            // a row count would reuse numbers if an earlier customer is ever deleted.
            $lastNumber = Customer::where('service_number', 'like', $prefix.'%')
                ->lockForUpdate()
                ->orderByRaw('LENGTH(service_number) DESC, service_number DESC')
                ->value('service_number');

            $nextSeq = 1;
            if ($lastNumber && preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $lastNumber, $matches)) {
                $nextSeq = ((int) $matches[1]) + 1;
            }

            return $prefix . $nextSeq;
        });
    }
}
