<?php

namespace App\Services;

use App\Models\Quotation;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class QuotationService
{
    public function __construct(
        private AuditService $auditService,
        private CoverageService $coverageService,
    ) {
    }

    public function create(array $data, User $actor, Request $request): Quotation
    {
        return DB::transaction(function () use ($data, $actor, $request) {
            $coverage = null;
            if (! empty($data['latitude']) && ! empty($data['longitude'])) {
                $coverage = $this->coverageService->check((float) $data['latitude'], (float) $data['longitude']);
            }

            $ppnRate = (float) Setting::get('ppn_rate', '11');
            $totals = $this->calculateTotals($data['items'], $ppnRate);

            $quotation = Quotation::create([
                'quotation_number' => $this->generateQuotationNumber(),
                'customer_id' => $data['customer_id'] ?? null,
                'prospect_name' => $data['prospect_name'],
                'prospect_company' => $data['prospect_company'] ?? null,
                'prospect_address' => $data['prospect_address'] ?? null,
                'prospect_phone' => $data['prospect_phone'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'is_covered' => $coverage['covered'] ?? null,
                'nearest_distance_km' => $coverage['distance_km'] ?? null,
                'nearest_point_name' => ($coverage['point'] ?? null)?->name,
                'subtotal' => $totals['subtotal'],
                'ppn_rate' => $ppnRate,
                'ppn_amount' => $totals['ppn_amount'],
                'total' => $totals['total'],
                'valid_until' => $data['valid_until'] ?? now()->addDays(14)->toDateString(),
                'notes' => $data['notes'] ?? null,
                'status' => 'DRAFT',
                'created_by' => $actor->id,
            ]);

            $quotation->items()->createMany($totals['items']);

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'QUOTATION',
                action: 'CREATE_QUOTATION',
                target: 'Quotation',
                targetId: $quotation->id,
                newValue: $quotation->only(['quotation_number', 'prospect_name', 'total']),
            );

            return $quotation;
        });
    }

    public function update(Quotation $quotation, array $data, User $actor, Request $request): Quotation
    {
        return DB::transaction(function () use ($quotation, $data, $actor, $request) {
            $old = $quotation->only(['prospect_name', 'prospect_company', 'prospect_address', 'prospect_phone', 'total', 'status']);

            $coverage = null;
            if (! empty($data['latitude']) && ! empty($data['longitude'])) {
                $coverage = $this->coverageService->check((float) $data['latitude'], (float) $data['longitude']);
            }

            $totals = $this->calculateTotals($data['items'], (float) $quotation->ppn_rate);

            $quotation->update([
                'customer_id' => $data['customer_id'] ?? null,
                'prospect_name' => $data['prospect_name'],
                'prospect_company' => $data['prospect_company'] ?? null,
                'prospect_address' => $data['prospect_address'] ?? null,
                'prospect_phone' => $data['prospect_phone'] ?? null,
                'latitude' => $data['latitude'] ?? null,
                'longitude' => $data['longitude'] ?? null,
                'is_covered' => $coverage['covered'] ?? $quotation->is_covered,
                'nearest_distance_km' => $coverage['distance_km'] ?? $quotation->nearest_distance_km,
                'nearest_point_name' => ($coverage['point'] ?? null)?->name ?? $quotation->nearest_point_name,
                'subtotal' => $totals['subtotal'],
                'ppn_amount' => $totals['ppn_amount'],
                'total' => $totals['total'],
                'valid_until' => $data['valid_until'] ?? $quotation->valid_until,
                'notes' => $data['notes'] ?? null,
                'status' => $data['status'] ?? $quotation->status,
            ]);

            $quotation->items()->delete();
            $quotation->items()->createMany($totals['items']);

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'QUOTATION',
                action: 'UPDATE_QUOTATION',
                target: 'Quotation',
                targetId: $quotation->id,
                oldValue: $old,
                newValue: $quotation->only(['prospect_name', 'prospect_company', 'prospect_address', 'prospect_phone', 'total', 'status']),
            );

            return $quotation;
        });
    }

    /**
     * @param array<int, array{service_package_id?: ?int, item_name: string, qty: int, unit_price: float}> $items
     * @return array{subtotal: float, ppn_amount: float, total: float, items: array}
     */
    private function calculateTotals(array $items, float $ppnRate): array
    {
        $subtotal = 0;
        $rows = [];

        foreach ($items as $item) {
            $lineSubtotal = round(((float) $item['unit_price']) * ((int) $item['qty']), 2);
            $subtotal += $lineSubtotal;

            $rows[] = [
                'service_package_id' => $item['service_package_id'] ?? null,
                'item_name' => $item['item_name'],
                'qty' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $lineSubtotal,
            ];
        }

        $ppnAmount = round($subtotal * ($ppnRate / 100), 2);

        return [
            'subtotal' => $subtotal,
            'ppn_amount' => $ppnAmount,
            'total' => $subtotal + $ppnAmount,
            'items' => $rows,
        ];
    }

    private function generateQuotationNumber(): string
    {
        $prefix = 'SPH/'.now()->format('Y/m').'/';

        // MAX-based (not COUNT-based) so a deleted quotation never causes a number to be reused.
        $lastNumber = Quotation::where('quotation_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('quotation_number')
            ->value('quotation_number');

        $nextSeq = $lastNumber ? ((int) substr($lastNumber, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }
}
