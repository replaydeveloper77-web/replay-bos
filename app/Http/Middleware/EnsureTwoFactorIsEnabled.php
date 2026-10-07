<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTwoFactorIsEnabled
{
    /**
     * Blocks access to the rest of the app for super-admin/finance accounts until
     * they've set up 2FA — those roles are sensitive enough that it isn't optional.
     * Doesn't apply to anyone else; they can enable 2FA voluntarily via Keamanan Akun.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! config('auth.two_factor_enabled', true)) {
            return $next($request);
        }

        $user = $request->user();

        if (! $user || ! $user->requiresTwoFactor() || $user->hasTwoFactorEnabled()) {
            return $next($request);
        }

        if ($request->routeIs('two-factor.setup', 'two-factor.confirm', 'logout')) {
            return $next($request);
        }

        return redirect()->route('two-factor.setup');
    }
}
