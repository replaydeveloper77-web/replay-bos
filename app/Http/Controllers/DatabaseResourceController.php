<?php

namespace App\Http\Controllers;

use App\Services\DatabaseResourceService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class DatabaseResourceController extends Controller
{
    public function __construct(
        private DatabaseResourceService $resourceService
    ) {
    }

    public function index(Request $request): View
    {
        abort_unless($request->user()->can('database.monitor'), 403, 'Anda tidak memiliki hak akses untuk monitoring resource database.');

        $search = $request->string('q')->trim()->value() ?: null;
        $sortBy = $request->string('sort')->value() ?: 'total_size';
        $sortDir = $request->string('dir')->value() ?: 'desc';

        $overview = $this->resourceService->getOverview();
        $tables = $this->resourceService->getTables($search, $sortBy, $sortDir);

        return view('database-resources.index', compact('overview', 'tables', 'search', 'sortBy', 'sortDir'));
    }

    public function optimize(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('database.monitor'), 403);

        $request->validate([
            'table' => ['required', 'string', 'max:64'],
        ]);

        try {
            $tableName = $request->input('table');
            $res = $this->resourceService->optimizeTable($tableName);

            return redirect()->back()->with('status', "Tabel `{$tableName}` berhasil dioptimasi ({$res['status']}: {$res['message']}).");
        } catch (Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal mengoptimasi tabel: ' . $e->getMessage()]);
        }
    }

    public function optimizeAll(Request $request): RedirectResponse
    {
        abort_unless($request->user()->can('database.monitor'), 403);

        try {
            $res = $this->resourceService->optimizeAllTables();

            return redirect()->back()->with('status', "Berhasil menjalankan optimasi pada {$res['optimized_count']} dari total {$res['total']} tabel database.");
        } catch (Throwable $e) {
            return redirect()->back()->withErrors(['error' => 'Gagal mengoptimasi tabel: ' . $e->getMessage()]);
        }
    }

    public function jsonStats(Request $request): JsonResponse
    {
        abort_unless($request->user()->can('database.monitor'), 403);

        return response()->json([
            'status' => 'success',
            'data' => $this->resourceService->getOverview(),
        ]);
    }
}
