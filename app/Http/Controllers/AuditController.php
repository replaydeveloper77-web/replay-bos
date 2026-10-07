<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('audit.view');

        $logs = AuditLog::with('user')
            ->when($request->filled('module'), fn ($q) => $q->where('module', $request->string('module')))
            ->latest('created_at')
            ->paginate(20)
            ->withQueryString();

        $modules = AuditLog::select('module')->distinct()->orderBy('module')->pluck('module');

        return view('audit.index', compact('logs', 'modules'));
    }
}
