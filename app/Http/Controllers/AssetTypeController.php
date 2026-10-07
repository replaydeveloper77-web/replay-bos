<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetTypeRequest;
use App\Http\Requests\UpdateAssetTypeRequest;
use App\Models\AssetType;
use App\Services\AssetTypeService;
use Illuminate\Http\Request;

class AssetTypeController extends Controller
{
    public function __construct(private AssetTypeService $assetTypeService)
    {
    }

    public function index()
    {
        $this->authorize('viewAny', AssetType::class);

        $assetTypes = AssetType::withCount('assets')->orderBy('name')->get();

        return view('asset-types.index', compact('assetTypes'));
    }

    public function create()
    {
        $this->authorize('create', AssetType::class);

        return view('asset-types.create');
    }

    public function store(StoreAssetTypeRequest $request)
    {
        $this->assetTypeService->create($request->validated(), $request->user(), $request);

        return redirect()->route('asset-types.index')->with('status', 'Jenis aset berhasil ditambahkan.');
    }

    public function edit(AssetType $assetType)
    {
        $this->authorize('update', AssetType::class);

        return view('asset-types.edit', ['assetType' => $assetType]);
    }

    public function update(UpdateAssetTypeRequest $request, AssetType $assetType)
    {
        $this->assetTypeService->update($assetType, $request->validated(), $request->user(), $request);

        return redirect()->route('asset-types.index')->with('status', 'Jenis aset berhasil diperbarui.');
    }

    public function disable(Request $request, AssetType $assetType)
    {
        $this->authorize('update', AssetType::class);

        $this->assetTypeService->setActive($assetType, false, $request->user(), $request);

        return redirect()->route('asset-types.index')->with('status', 'Jenis aset dinonaktifkan.');
    }

    public function enable(Request $request, AssetType $assetType)
    {
        $this->authorize('update', AssetType::class);

        $this->assetTypeService->setActive($assetType, true, $request->user(), $request);

        return redirect()->route('asset-types.index')->with('status', 'Jenis aset diaktifkan.');
    }
}
