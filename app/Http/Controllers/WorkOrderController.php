<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeWorkOrderStatusRequest;
use App\Http\Requests\StoreWorkOrderRequest;
use App\Http\Requests\UpdateWorkOrderRequest;
use App\Models\Customer;
use App\Models\Team;
use App\Models\User;
use App\Models\WorkOrder;
use App\Services\WorkOrderService;
use Illuminate\Http\Request;
use RuntimeException;

class WorkOrderController extends Controller
{
    public function __construct(private WorkOrderService $workOrderService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', WorkOrder::class);

        $workOrders = WorkOrder::with('customer', 'assignedTo', 'team')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = '%'.$request->string('q').'%';
                $query->where('work_order_number', 'like', $search)
                    ->orWhereHas('customer', fn ($q) => $q->where('name', 'like', $search));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        return view('work-orders.index', compact('workOrders'));
    }

    public function show(WorkOrder $workOrder)
    {
        $this->authorize('view', WorkOrder::class);

        $workOrder->load('customer', 'assignedTo', 'team', 'completedBy', 'createdBy');

        return view('work-orders.show', compact('workOrder'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', WorkOrder::class);

        $customers = Customer::orderBy('name')->get(['id', 'name', 'company', 'activation_status']);
        $technicians = $this->technicians();
        $teams = Team::where('is_active', true)->orderBy('name')->get();
        $selectedCustomerId = $request->integer('customer_id') ?: null;

        return view('work-orders.create', compact('customers', 'technicians', 'teams', 'selectedCustomerId'));
    }

    public function store(StoreWorkOrderRequest $request)
    {
        $workOrder = $this->workOrderService->create($request->validated(), $request->user(), $request);

        return redirect()->route('work-orders.show', $workOrder)->with('status', 'Work order berhasil dibuat.');
    }

    public function edit(WorkOrder $workOrder)
    {
        $this->authorize('update', WorkOrder::class);

        $technicians = $this->technicians();
        $teams = Team::where('is_active', true)->orderBy('name')->get();

        return view('work-orders.edit', compact('workOrder', 'technicians', 'teams'));
    }

    public function update(UpdateWorkOrderRequest $request, WorkOrder $workOrder)
    {
        $this->workOrderService->update($workOrder, $request->validated(), $request->user(), $request);

        return redirect()->route('work-orders.show', $workOrder)->with('status', 'Work order berhasil diperbarui.');
    }

    public function changeStatus(ChangeWorkOrderStatusRequest $request, WorkOrder $workOrder)
    {
        try {
            $this->workOrderService->changeStatus($workOrder, $request->validated()['status'], $request->validated(), $request->user(), $request);
        } catch (RuntimeException $e) {
            return back()->with('status', $e->getMessage());
        }

        return redirect()->route('work-orders.show', $workOrder)->with('status', 'Status work order berhasil diperbarui.');
    }

    private function technicians()
    {
        return User::whereHas('roles', fn ($q) => $q->whereIn('slug', ['noc-engineer', 'noc-admin', 'teknisi']))
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'username']);
    }
}
