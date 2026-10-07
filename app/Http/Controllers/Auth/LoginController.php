<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\LoginLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function __construct(private LoginLogService $loginLogService)
    {
    }

    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        $user = User::where('username', $credentials['username'])->first();

        if (! $user) {
            $this->loginLogService->recordFailure(null, $credentials['username'], 'Username tidak ditemukan', $request);

            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        if (! $user->is_active) {
            $this->loginLogService->recordFailure($user, $credentials['username'], 'Akun dinonaktifkan', $request);

            throw ValidationException::withMessages([
                'username' => 'Akun Anda telah dinonaktifkan. Hubungi NOC Admin.',
            ]);
        }

        if (! Auth::validate(['username' => $credentials['username'], 'password' => $credentials['password']])) {
            $this->loginLogService->recordFailure($user, $credentials['username'], 'Password salah', $request);

            throw ValidationException::withMessages([
                'username' => 'Username atau password salah.',
            ]);
        }

        if ($user->hasTwoFactorEnabled()) {
            $request->session()->put('2fa.user_id', $user->id);
            $request->session()->put('2fa.remember', $request->boolean('remember'));

            return redirect()->route('two-factor.challenge');
        }

        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();
        $this->loginLogService->recordSuccess($user, $request);

        return redirect()->intended(route($user->landingRouteName()));
    }

    public function logout(Request $request)
    {
        $user = $request->user();

        Auth::logout();

        if ($user) {
            $this->loginLogService->recordLogout($user, $request);
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
