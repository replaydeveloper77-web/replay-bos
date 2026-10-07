<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChangeAssetStatusRequest;
use App\Http\Requests\StoreAssetRequest;
use App\Http\Requests\UpdateAssetRequest;
use App\Models\Asset;
use App\Models\AssetType;
use App\Models\CoveragePoint;
use App\Models\Customer;
use App\Services\AssetService;
use Illuminate\Http\Request;
use RuntimeException;

class AssetController extends Controller
{
    public function __construct(private AssetService $assetService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Asset::class);

        $assets = Asset::with('assetType', 'customer', 'coveragePoint')
            ->when($request->filled('q'), function ($query) use ($request) {
                $search = '%'.$request->string('q').'%';
                $query->where(fn ($q) => $q->where('asset_code', 'like', $search)
                    ->orWhere('serial_number', 'like', $search));
            })
            ->when($request->filled('status'), fn ($query) => $query->where('status', $request->string('status')))
            ->when($request->filled('asset_type_id'), fn ($query) => $query->where('asset_type_id', $request->integer('asset_type_id')))
            ->orderByDesc('created_at')
            ->paginate(20)
            ->withQueryString();

        $assetTypes = AssetType::orderBy('name')->get();

        return view('assets.index', compact('assets', 'assetTypes'));
    }

    public function show(Asset $asset)
    {
        $this->authorize('view', Asset::class);

        $asset->load('assetType', 'customer', 'coveragePoint', 'createdBy');

        $customers = Customer::where('is_active', true)->orderBy('name')->get(['id', 'name', 'company']);
        $coveragePoints = CoveragePoint::where('is_active', true)->orderBy('name')->get(['id', 'name']);

        return view('assets.show', compact('asset', 'customers', 'coveragePoints'));
    }

    public function create()
    {
        $this->authorize('create', Asset::class);

        $assetTypes = AssetType::where('is_active', true)->orderBy('name')->get();

        return view('assets.create', compact('assetTypes'));
    }

    public function store(StoreAssetRequest $request)
    {
        $asset = $this->assetService->create($request->validated(), $request->user(), $request);

        return redirect()->route('assets.show', $asset)->with('status', 'Aset berhasil dicatat.');
    }

    public function edit(Asset $asset)
    {
        $this->authorize('update', Asset::class);

        return view('assets.edit', compact('asset'));
    }

    public function update(UpdateAssetRequest $request, Asset $asset)
    {
        $this->assetService->update($asset, $request->validated(), $request->user(), $request);

        return redirect()->route('assets.show', $asset)->with('status', 'Aset berhasil diperbarui.');
    }

    public function changeStatus(ChangeAssetStatusRequest $request, Asset $asset)
    {
        try {
            $this->assetService->changeStatus($asset, $request->validated()['status'], $request->validated(), $request->user(), $request);
        } catch (RuntimeException $e) {
            return back()->with('status', $e->getMessage());
        }

        return redirect()->route('assets.show', $asset)->with('status', 'Status aset berhasil diperbarui.');
    }
}
