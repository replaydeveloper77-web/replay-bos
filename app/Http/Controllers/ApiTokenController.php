<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreApiTokenRequest;
use App\Services\AuditService;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function index(Request $request)
    {
        $this->authorize('api.manage');

        $tokens = $request->user()->tokens()->latest()->get();

        return view('api-tokens.index', compact('tokens'));
    }

    public function store(StoreApiTokenRequest $request)
    {
        $result = $request->user()->createToken($request->validated()['name']);

        $this->auditService->log(
            actor: $request->user(),
            request: $request,
            module: 'API_TOKEN',
            action: 'CREATE_API_TOKEN',
            target: 'PersonalAccessToken',
            targetId: $result->accessToken->id,
            newValue: ['name' => $result->accessToken->name],
        );

        return redirect()->route('api-tokens.index')->with('plainTextToken', $result->plainTextToken);
    }

    public function destroy(Request $request, int $token)
    {
        $this->authorize('api.manage');

        $accessToken = $request->user()->tokens()->findOrFail($token);

        $this->auditService->log(
            actor: $request->user(),
            request: $request,
            module: 'API_TOKEN',
            action: 'REVOKE_API_TOKEN',
            target: 'PersonalAccessToken',
            targetId: $accessToken->id,
            oldValue: ['name' => $accessToken->name],
        );

        $accessToken->delete();

        return redirect()->route('api-tokens.index')->with('status', 'Token berhasil dicabut.');
    }
}
