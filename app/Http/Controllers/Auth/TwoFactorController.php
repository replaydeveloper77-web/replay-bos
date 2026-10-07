<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\LoginLogService;
use App\Services\TwoFactorService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class TwoFactorController extends Controller
{
    public function __construct(
        private TwoFactorService $twoFactorService,
        private LoginLogService $loginLogService,
    ) {
    }

    /**
     * Setup screen — shown either voluntarily (Keamanan Akun) or forced by
     * EnsureTwoFactorIsEnabled for super-admin/finance accounts without 2FA yet.
     */
    public function setup(Request $request)
    {
        if (! config('auth.two_factor_enabled', true)) {
            return redirect()->route('dashboard')->with('status', '2FA dinonaktifkan di environment ini.');
        }

        $user = $request->user();

        if ($user->hasTwoFactorEnabled()) {
            return view('auth.two-factor-setup', ['secret' => null, 'qrSvg' => null, 'forced' => false]);
        }

        // Keep the pending secret in the session until the user proves they scanned it
        // correctly — nothing touches the users table until confirm() succeeds.
        $secret = $request->session()->get('2fa.pending_secret') ?? $this->twoFactorService->generateSecretKey();
        $request->session()->put('2fa.pending_secret', $secret);

        $qrSvg = $this->twoFactorService->getQrCodeSvg($user, $secret);

        return view('auth.two-factor-setup', [
            'secret' => $secret,
            'qrSvg' => $qrSvg,
            'forced' => $user->requiresTwoFactor(),
        ]);
    }

    public function confirm(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);

        $secret = $request->session()->get('2fa.pending_secret');

        if (! $secret || ! $this->twoFactorService->verifyCode($secret, $request->string('code'))) {
            throw ValidationException::withMessages(['code' => 'Kode salah atau kadaluarsa. Coba lagi.']);
        }

        $this->twoFactorService->confirm($request->user(), $secret);
        $request->session()->forget('2fa.pending_secret');

        return redirect()->route('dashboard')->with('status', '2FA berhasil diaktifkan.');
    }

    public function disable(Request $request)
    {
        $request->validate(['password' => ['required', 'string']]);

        if (! Hash::check($request->string('password'), $request->user()->password)) {
            throw ValidationException::withMessages(['password' => 'Password salah.']);
        }

        $this->twoFactorService->disable($request->user());

        return redirect()->route('two-factor.setup')->with('status', '2FA dinonaktifkan.');
    }

    /**
     * Second step of login — reached only via the session marker LoginController sets
     * after username/password already checked out. Not behind the 'auth' middleware,
     * since the user isn't fully logged in yet.
     */
    public function challenge(Request $request)
    {
        if (! config('auth.two_factor_enabled', true)) {
            if ($userId = $request->session()->pull('2fa.user_id')) {
                $user = \App\Models\User::find($userId);
                if ($user) {
                    $remember = $request->session()->pull('2fa.remember', false);
                    Auth::login($user, $remember);
                    $request->session()->regenerate();
                    $user->forceFill(['last_login_at' => now()])->save();
                    $this->loginLogService->recordSuccess($user, $request);

                    return redirect()->intended(route($user->landingRouteName()));
                }
            }

            return redirect()->route('login');
        }

        if (! $request->session()->has('2fa.user_id')) {
            return redirect()->route('login');
        }

        return view('auth.two-factor-challenge');
    }

    public function verifyChallenge(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);

        $userId = $request->session()->get('2fa.user_id');

        if (! $userId) {
            return redirect()->route('login');
        }

        $user = \App\Models\User::findOrFail($userId);

        if (! $this->twoFactorService->verifyCode($user->two_factor_secret, $request->string('code'))) {
            $this->loginLogService->recordFailure($user, $user->username, 'Kode 2FA salah', $request);

            throw ValidationException::withMessages(['code' => 'Kode salah. Coba lagi.']);
        }

        $remember = $request->session()->pull('2fa.remember', false);
        $request->session()->forget('2fa.user_id');

        Auth::login($user, $remember);
        $request->session()->regenerate();

        $user->forceFill(['last_login_at' => now()])->save();
        $this->loginLogService->recordSuccess($user, $request);

        return redirect()->intended(route($user->landingRouteName()));
    }
}
