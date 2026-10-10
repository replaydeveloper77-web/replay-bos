<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePackageChangeRequest;
use App\Models\Customer;
use App\Models\PackageChangeRequest;
use App\Models\ServicePackage;
use App\Services\PackageChangeRequestService;
use Illuminate\Http\Request;

class PackageChangeRequestController extends Controller
{
    public function __construct(
        private PackageChangeRequestService $packageChangeService
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', PackageChangeRequest::class);

        $query = PackageChangeRequest::with(['customer', 'currentPackage', 'targetPackage', 'requestedBy', 'approvedBy', 'workOrder'])
            ->when($request->filled('q'), function ($q) use ($request) {
                $search = $request->string('q')->trim();
                $q->where(function ($sub) use ($search) {
                    $sub->where('request_number', 'like', "%{$search}%")
                        ->orWhereHas('customer', function ($cq) use ($search) {
                            $cq->where('name', 'like', "%{$search}%")
                                ->orWhere('service_number', 'like', "%{$search}%");
                        })
                        ->orWhereHas('workOrder', function ($wq) use ($search) {
                            $wq->where('work_order_number', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')));

        $stats = [
            'total' => PackageChangeRequest::count(),
            'pending' => PackageChangeRequest::where('status', 'PENDING')->count(),
            'approved' => PackageChangeRequest::where('status', 'APPROVED')->count(),
            'rejected' => PackageChangeRequest::where('status', 'REJECTED')->count(),
        ];

        $packageChanges = $query->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        return view('package-changes.index', compact('packageChanges', 'stats'));
    }

    public function create(Request $request)
    {
        $this->authorize('create', PackageChangeRequest::class);

        $customers = Customer::orderBy('name')
            ->with('servicePackage')
            ->get(['id', 'name', 'category', 'service', 'service_number', 'service_package_id', 'subscription_price']);

        $servicePackages = ServicePackage::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'price', 'speed_label']);

        $selectedCustomerId = $request->integer('customer_id') ?: null;

        return view('package-changes.create', compact('customers', 'servicePackages', 'selectedCustomerId'));
    }

    public function store(StorePackageChangeRequest $request)
    {
        $changeRequest = $this->packageChangeService->submit($request->validated(), $request->user(), $request);

        return redirect()->route('package-changes.index')
            ->with('status', 'Permohonan ' . $changeRequest->typeLabel() . ' (' . $changeRequest->request_number . ') berhasil diajukan dan sedang menunggu persetujuan Finance.');
    }

    public function show(PackageChangeRequest $packageChange)
    {
        $this->authorize('view', $packageChange);

        $packageChange->load(['customer', 'currentPackage', 'targetPackage', 'requestedBy', 'approvedBy', 'workOrder']);

        return view('package-changes.show', compact('packageChange'));
    }

    public function approve(Request $request, PackageChangeRequest $packageChange)
    {
        $this->authorize('approve', $packageChange);

        $data = $request->validate([
            'finance_notes' => ['nullable', 'string', 'max:1000'],
            'new_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $this->packageChangeService->approve(
            $packageChange,
            $data['finance_notes'] ?? null,
            $request->user(),
            $request,
            isset($data['new_price']) && $data['new_price'] !== '' ? (float) $data['new_price'] : null
        );

        $packageChange->refresh()->load('workOrder');
        $woNumber = $packageChange->workOrder?->work_order_number;

        return redirect()->back()
            ->with('status', "Permohonan {$packageChange->typeLabel()} ({$packageChange->request_number}) telah DISETUJUI. Nomor Work Order {$woNumber} berhasil digenerate otomatis!");
    }

    public function reject(Request $request, PackageChangeRequest $packageChange)
    {
        $this->authorize('reject', $packageChange);

        $data = $request->validate([
            'finance_notes' => ['required', 'string', 'max:1000'],
        ], [
            'finance_notes.required' => 'Wajib mengisi alasan penolakan.',
        ]);

        $this->packageChangeService->reject(
            $packageChange,
            $data['finance_notes'],
            $request->user(),
            $request
        );

        return redirect()->back()
            ->with('status', "Permohonan {$packageChange->typeLabel()} ({$packageChange->request_number}) telah DITOLAK oleh Finance.");
    }

    public function cancel(Request $request, PackageChangeRequest $packageChange)
    {
        $this->authorize('cancel', $packageChange);

        $this->packageChangeService->cancel($packageChange, $request->user(), $request);

        return redirect()->back()
            ->with('status', "Permohonan {$packageChange->typeLabel()} ({$packageChange->request_number}) telah dibatalkan.");
    }
}
