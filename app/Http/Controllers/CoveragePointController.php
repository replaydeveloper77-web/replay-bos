<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCoveragePointRequest;
use App\Http\Requests\UpdateCoveragePointRequest;
use App\Models\CoveragePoint;
use App\Services\CoveragePointService;
use Illuminate\Http\Request;

class CoveragePointController extends Controller
{
    public function __construct(private CoveragePointService $coveragePointService)
    {
    }

    public function index()
    {
        $this->authorize('viewAny', CoveragePoint::class);

        $points = CoveragePoint::orderBy('name')->get();

        $pointsJson = $points->map(fn ($p) => [
            'name' => $p->name,
            'lat' => (float) $p->latitude,
            'lng' => (float) $p->longitude,
            'radius_km' => (float) $p->radius_km,
            'active' => $p->is_active,
        ])->values();

        return view('coverage-points.index', compact('points', 'pointsJson'));
    }

    public function create()
    {
        $this->authorize('create', CoveragePoint::class);

        return view('coverage-points.create');
    }

    public function store(StoreCoveragePointRequest $request)
    {
        $this->coveragePointService->create($request->validated(), $request->user(), $request);

        return redirect()->route('coverage-points.index')->with('status', 'Titik coverage berhasil ditambahkan.');
    }

    public function edit(CoveragePoint $coveragePoint)
    {
        $this->authorize('update', CoveragePoint::class);

        return view('coverage-points.edit', ['point' => $coveragePoint]);
    }

    public function update(UpdateCoveragePointRequest $request, CoveragePoint $coveragePoint)
    {
        $this->coveragePointService->update($coveragePoint, $request->validated(), $request->user(), $request);

        return redirect()->route('coverage-points.index')->with('status', 'Titik coverage berhasil diperbarui.');
    }

    public function disable(Request $request, CoveragePoint $coveragePoint)
    {
        $this->authorize('update', CoveragePoint::class);

        $this->coveragePointService->setActive($coveragePoint, false, $request->user(), $request);

        return redirect()->route('coverage-points.index')->with('status', 'Titik coverage dinonaktifkan.');
    }

    public function enable(Request $request, CoveragePoint $coveragePoint)
    {
        $this->authorize('update', CoveragePoint::class);

        $this->coveragePointService->setActive($coveragePoint, true, $request->user(), $request);

        return redirect()->route('coverage-points.index')->with('status', 'Titik coverage diaktifkan.');
    }
}
