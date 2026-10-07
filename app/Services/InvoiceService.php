<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Quotation;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class InvoiceService
{
    public function __construct(
        private AuditService $auditService,
        private CustomerService $customerService,
    ) {
    }

    /**
     * "Closing" a quotation: lock in the final agreed price (may differ from the
     * original quotation), generate an invoice, and push the prospect into the
     * Pelanggan list flagged for NOC to physically activate.
     *
     * @param array<int, array{service_package_id?: ?int, item_name: string, qty: int, unit_price: float}> $items
     */
    public function close(Quotation $quotation, array $items, User $actor, Request $request): Invoice
    {
        if ($quotation->invoice()->exists()) {
            throw new RuntimeException('Penawaran ini sudah pernah di-closing.');
        }

        return DB::transaction(function () use ($quotation, $items, $actor, $request) {
            $totals = $this->calculateTotals($items, (float) $quotation->ppn_rate);

            $customer = $quotation->customer;

            if (! $customer) {
                $customer = $this->customerService->create([
                    'name' => $quotation->prospect_name,
                    'company' => $quotation->prospect_company,
                    'whatsapp' => $quotation->prospect_phone,
                    'location' => $quotation->prospect_address,
                    'service' => $items[0]['item_name'] ?? null,
                    'notes' => "Otomatis dibuat dari closing {$quotation->quotation_number}. Menunggu aktivasi NOC.",
                    'activation_status' => 'PENDING',
                ], $actor, $request);
            }

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'quotation_id' => $quotation->id,
                'customer_id' => $customer->id,
                'type' => 'CLOSING',
                'due_date' => $quotation->valid_until ?? now()->addDays(14),
                'subtotal' => $totals['subtotal'],
                'ppn_rate' => $quotation->ppn_rate,
                'ppn_amount' => $totals['ppn_amount'],
                'total' => $totals['total'],
                'status' => 'UNPAID',
                'created_by' => $actor->id,
            ]);

            $invoice->items()->createMany($totals['items']);

            $quotation->update([
                'status' => 'CLOSED',
                'customer_id' => $customer->id,
            ]);

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'INVOICE',
                action: 'CLOSE_QUOTATION',
                target: 'Invoice',
                targetId: $invoice->id,
                newValue: [
                    'quotation_number' => $quotation->quotation_number,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_id' => $customer->id,
                    'total' => $totals['total'],
                ],
            );

            return $invoice;
        });
    }

    /**
     * Manual invoice — used by Finance for package upgrades or any one-off billing
     * that isn't tied to a Marketing quotation.
     *
     * @param array<int, array{service_package_id?: ?int, item_name: string, qty: int, unit_price: float}> $items
     */
    public function createManual(Customer $customer, array $items, string $type, string $dueDate, User $actor, Request $request): Invoice
    {
        return DB::transaction(function () use ($customer, $items, $type, $dueDate, $actor, $request) {
            $ppnRate = (float) Setting::get('ppn_rate', '11');
            $totals = $this->calculateTotals($items, $ppnRate);

            $invoice = Invoice::create([
                'invoice_number' => $this->generateInvoiceNumber(),
                'customer_id' => $customer->id,
                'type' => $type,
                'due_date' => $dueDate,
                'subtotal' => $totals['subtotal'],
                'ppn_rate' => $ppnRate,
                'ppn_amount' => $totals['ppn_amount'],
                'total' => $totals['total'],
                'status' => 'UNPAID',
                'created_by' => $actor->id,
            ]);

            $invoice->items()->createMany($totals['items']);

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'INVOICE',
                action: 'CREATE_MANUAL_INVOICE',
                target: 'Invoice',
                targetId: $invoice->id,
                newValue: [
                    'invoice_number' => $invoice->invoice_number,
                    'customer_id' => $customer->id,
                    'type' => $type,
                    'total' => $totals['total'],
                ],
            );

            return $invoice;
        });
    }

    /**
     * Generate one recurring monthly invoice per billed customer for the given period,
     * skipping anyone who already has a RECURRING invoice for that period.
     *
     * @return Collection<int, Invoice>
     */
    public function generateMonthly(Carbon $period, User $actor, Request $request): Collection
    {
        $billingPeriod = $period->format('Y-m');
        $ppnRate = (float) Setting::get('ppn_rate', '11');

        $customers = Customer::where('is_active', true)
            ->whereNotNull('subscription_price')
            ->where('subscription_price', '>', 0)
            ->whereDoesntHave('invoices', fn ($q) => $q->where('type', 'RECURRING')->where('billing_period', $billingPeriod))
            ->with('servicePackage')
            ->get();

        return DB::transaction(function () use ($customers, $billingPeriod, $period, $ppnRate, $actor, $request) {
            $created = collect();

            foreach ($customers as $customer) {
                $daysInMonth = $period->daysInMonth;
                $dueDay = min($customer->billing_due_day, $daysInMonth);
                $dueDate = $period->copy()->day($dueDay);

                $itemName = 'Tagihan Bulanan — '.($customer->servicePackage?->name ?? $customer->service ?? 'Langganan Internet').' — '.$period->translatedFormat('F Y');

                $totals = $this->calculateTotals([[
                    'service_package_id' => $customer->service_package_id,
                    'item_name' => $itemName,
                    'qty' => 1,
                    'unit_price' => (float) $customer->subscription_price,
                ]], $ppnRate);

                $invoice = Invoice::create([
                    'invoice_number' => $this->generateInvoiceNumber(),
                    'customer_id' => $customer->id,
                    'type' => 'RECURRING',
                    'billing_period' => $billingPeriod,
                    'due_date' => $dueDate,
                    'subtotal' => $totals['subtotal'],
                    'ppn_rate' => $ppnRate,
                    'ppn_amount' => $totals['ppn_amount'],
                    'total' => $totals['total'],
                    'status' => 'UNPAID',
                    'created_by' => $actor->id,
                ]);

                $invoice->items()->createMany($totals['items']);

                $created->push($invoice);
            }

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'INVOICE',
                action: 'GENERATE_MONTHLY_INVOICES',
                target: 'Invoice',
                newValue: ['billing_period' => $billingPeriod, 'count' => $created->count()],
            );

            return $created;
        });
    }

    public function markPaid(Invoice $invoice, User $actor, Request $request): Invoice
    {
        $invoice->update(['status' => 'PAID', 'paid_at' => now(), 'paid_by' => $actor->id]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'INVOICE',
            action: 'MARK_INVOICE_PAID',
            target: 'Invoice',
            targetId: $invoice->id,
            newValue: ['status' => 'PAID'],
        );

        return $invoice;
    }

    public function markUnpaid(Invoice $invoice, User $actor, Request $request): Invoice
    {
        $invoice->update(['status' => 'UNPAID', 'paid_at' => null, 'paid_by' => null]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'INVOICE',
            action: 'MARK_INVOICE_UNPAID',
            target: 'Invoice',
            targetId: $invoice->id,
            newValue: ['status' => 'UNPAID'],
        );

        return $invoice;
    }

    /**
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

    private function generateInvoiceNumber(): string
    {
        $prefix = 'INV/'.now()->format('Y/m').'/';

        // MAX-based (not COUNT-based) so a deleted invoice never causes a number to be reused.
        $lastNumber = Invoice::where('invoice_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByDesc('invoice_number')
            ->value('invoice_number');

        $nextSeq = $lastNumber ? ((int) substr($lastNumber, strlen($prefix))) + 1 : 1;

        return $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }
}
