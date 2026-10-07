<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class ChangePasswordController extends Controller
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function edit()
    {
        return view('auth.change-password');
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (! Hash::check($data['current_password'], $user->password)) {
            throw ValidationException::withMessages(['current_password' => 'Password saat ini salah.']);
        }

        $user->password = Hash::make($data['new_password']);
        $user->save();

        $this->auditService->log(
            actor: $user,
            request: $request,
            module: 'SECURITY',
            action: 'CHANGE_OWN_PASSWORD',
            target: 'User',
            targetId: $user->id,
        );

        return redirect()->route('password.edit')->with('status', 'Password berhasil diganti.');
    }
}
