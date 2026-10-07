<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerIpAllocationRequest;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerIpAllocationRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Models\CustomerIpAllocation;
use App\Models\ServicePackage;
use App\Services\AuditService;
use App\Services\CustomerService;
use Illuminate\Http\Request;
use RuntimeException;

class CustomerController extends Controller
{
    public function __construct(
        private CustomerService $customerService,
        private AuditService $auditService,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Customer::class);

        $customers = Customer::withCount(['tickets', 'quotations', 'invoices'])
            ->with('ipAllocations')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = '%'.$request->string('q').'%';
                $query->where(fn ($q) => $q->where('name', 'like', $search)
                    ->orWhere('company', 'like', $search)
                    ->orWhere('service_number', 'like', $search)
                    ->orWhereHas('ipAllocations', fn ($ipQuery) => $ipQuery->where('ip_address', 'like', $search)));
            })
            ->when($request->filled('category'), fn ($query) => $query->where('category', $request->string('category')))
            ->orderBy('name')
            ->paginate(20)
            ->withQueryString();

        return view('customers.index', compact('customers'));
    }

    public function show(Customer $customer)
    {
        $this->authorize('view', Customer::class);

        $customer->load('ipAllocations.createdBy', 'createdBy', 'servicePackage', 'reseller');
        $tickets = $customer->tickets()->with('pic')->latest('created_at')->get();
        $invoices = $customer->invoices()->latest('created_at')->get();
        $workOrders = $customer->workOrders()->with('assignedTo', 'team')->latest('created_at')->get();
        $homeCustomers = $customer->category === 'RESELLER'
            ? $customer->homeCustomers()->orderBy('name')->get()
            : collect();

        return view('customers.show', compact('customer', 'tickets', 'invoices', 'workOrders', 'homeCustomers'));
    }

    public function create()
    {
        $this->authorize('create', Customer::class);

        $packages = ServicePackage::where('is_active', true)->orderBy('name')->get();
        $resellers = Customer::where('category', 'RESELLER')->where('is_active', true)->orderBy('name')->get();

        return view('customers.create', compact('packages', 'resellers'));
    }

    public function store(StoreCustomerRequest $request)
    {
        $this->customerService->create($request->validated(), $request->user(), $request);

        return redirect()->route('customers.index')->with('status', 'Pelanggan berhasil ditambahkan.');
    }

    public function edit(Customer $customer)
    {
        $this->authorize('update', Customer::class);

        $tickets = $customer->tickets()->with('pic')->latest('created_at')->get();
        $customer->load('ipAllocations.createdBy');
        $packages = ServicePackage::where('is_active', true)->orderBy('name')->get();
        $resellers = Customer::where('category', 'RESELLER')->where('is_active', true)->where('id', '!=', $customer->id)->orderBy('name')->get();

        return view('customers.edit', compact('customer', 'tickets', 'packages', 'resellers'));
    }

    public function update(UpdateCustomerRequest $request, Customer $customer)
    {
        $this->customerService->update($customer, $request->validated(), $request->user(), $request);

        return redirect()->route('customers.index')->with('status', 'Pelanggan berhasil diperbarui.');
    }

    public function disable(Request $request, Customer $customer)
    {
        $this->authorize('update', Customer::class);

        $this->customerService->setActive($customer, false, $request->user(), $request);

        return redirect()->route('customers.index')->with('status', 'Pelanggan berhasil dinonaktifkan.');
    }

    public function enable(Request $request, Customer $customer)
    {
        $this->authorize('update', Customer::class);

        $this->customerService->setActive($customer, true, $request->user(), $request);

        return redirect()->route('customers.index')->with('status', 'Pelanggan berhasil diaktifkan.');
    }

    public function destroy(Request $request, Customer $customer)
    {
        $this->authorize('delete', Customer::class);

        $customerName = $customer->name;

        try {
            $this->customerService->delete($customer, $request->user(), $request);
        } catch (RuntimeException $e) {
            return back()->with('status', $e->getMessage());
        }

        return redirect()->route('customers.index')->with('status', "Pelanggan {$customerName} berhasil dihapus.");
    }

    public function activate(Request $request, Customer $customer)
    {
        $this->authorize('activate', Customer::class);

        $this->customerService->markActivated($customer, $request->user(), $request);

        return back()->with('status', 'Pelanggan ditandai sudah selesai diaktivasi.');
    }

    public function storeIpAllocation(StoreCustomerIpAllocationRequest $request, Customer $customer)
    {
        $allocation = CustomerIpAllocation::create([
            'customer_id' => $customer->id,
            'ip_address' => $request->validated()['ip_address'],
            'label' => $request->validated()['label'] ?? null,
            'created_by' => $request->user()->id,
        ]);

        $this->auditService->log(
            actor: $request->user(),
            request: $request,
            module: 'CUSTOMER',
            action: 'ADD_IP_ALLOCATION',
            target: 'CustomerIpAllocation',
            targetId: $allocation->id,
            newValue: ['customer_id' => $customer->id, 'ip_address' => $allocation->ip_address],
        );

        return redirect()->route('customers.edit', $customer)->with('status', 'IP berhasil dialokasikan.');
    }

    public function updateIpAllocation(UpdateCustomerIpAllocationRequest $request, Customer $customer, CustomerIpAllocation $allocation)
    {
        abort_unless($allocation->customer_id === $customer->id, 404);

        $old = $allocation->only(['ip_address', 'label']);

        $allocation->update([
            'ip_address' => $request->validated()['ip_address'],
            'label' => $request->validated()['label'] ?? null,
        ]);

        $this->auditService->log(
            actor: $request->user(),
            request: $request,
            module: 'CUSTOMER',
            action: 'UPDATE_IP_ALLOCATION',
            target: 'CustomerIpAllocation',
            targetId: $allocation->id,
            oldValue: $old,
            newValue: $allocation->only(['ip_address', 'label']),
        );

        return redirect()->route('customers.edit', $customer)->with('status', 'Alokasi IP berhasil diperbarui.');
    }

    public function destroyIpAllocation(Request $request, Customer $customer, CustomerIpAllocation $allocation)
    {
        $this->authorize('update', Customer::class);
        abort_unless($allocation->customer_id === $customer->id, 404);

        $this->auditService->log(
            actor: $request->user(),
            request: $request,
            module: 'CUSTOMER',
            action: 'DELETE_IP_ALLOCATION',
            target: 'CustomerIpAllocation',
            targetId: $allocation->id,
            oldValue: ['customer_id' => $customer->id, 'ip_address' => $allocation->ip_address],
        );

        $allocation->delete();

        return redirect()->route('customers.edit', $customer)->with('status', 'Alokasi IP berhasil dihapus.');
    }
}
