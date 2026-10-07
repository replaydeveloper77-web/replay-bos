<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreServicePackageRequest;
use App\Http\Requests\UpdateServicePackageRequest;
use App\Models\ServicePackage;
use App\Services\ServicePackageService;
use Illuminate\Http\Request;

class ServicePackageController extends Controller
{
    public function __construct(private ServicePackageService $servicePackageService)
    {
    }

    public function index()
    {
        $this->authorize('viewAny', ServicePackage::class);

        $packages = ServicePackage::orderBy('name')->get();

        return view('service-packages.index', compact('packages'));
    }

    public function create()
    {
        $this->authorize('create', ServicePackage::class);

        return view('service-packages.create');
    }

    public function store(StoreServicePackageRequest $request)
    {
        $this->servicePackageService->create($request->validated(), $request->user(), $request);

        return redirect()->route('service-packages.index')->with('status', 'Paket layanan berhasil ditambahkan.');
    }

    public function edit(ServicePackage $servicePackage)
    {
        $this->authorize('update', ServicePackage::class);

        return view('service-packages.edit', ['package' => $servicePackage]);
    }

    public function update(UpdateServicePackageRequest $request, ServicePackage $servicePackage)
    {
        $this->servicePackageService->update($servicePackage, $request->validated(), $request->user(), $request);

        return redirect()->route('service-packages.index')->with('status', 'Paket layanan berhasil diperbarui.');
    }

    public function disable(Request $request, ServicePackage $servicePackage)
    {
        $this->authorize('update', ServicePackage::class);

        $this->servicePackageService->setActive($servicePackage, false, $request->user(), $request);

        return redirect()->route('service-packages.index')->with('status', 'Paket layanan dinonaktifkan.');
    }

    public function enable(Request $request, ServicePackage $servicePackage)
    {
        $this->authorize('update', ServicePackage::class);

        $this->servicePackageService->setActive($servicePackage, true, $request->user(), $request);

        return redirect()->route('service-packages.index')->with('status', 'Paket layanan diaktifkan.');
    }
}
