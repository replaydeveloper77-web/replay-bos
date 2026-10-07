<?php

namespace App\Services;

use App\Models\LoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class LoginLogService
{
    public function recordSuccess(User $user, Request $request): LoginLog
    {
        return LoginLog::create([
            'user_id' => $user->id,
            'username_attempted' => $user->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'login_at' => now(),
            'status' => 'SUCCESS',
        ]);
    }

    public function recordFailure(?User $user, string $usernameAttempted, string $reason, Request $request): LoginLog
    {
        return LoginLog::create([
            'user_id' => $user?->id,
            'username_attempted' => $usernameAttempted,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'FAILED',
            'failure_reason' => $reason,
        ]);
    }

    public function recordLogout(User $user, Request $request): LoginLog
    {
        return LoginLog::create([
            'user_id' => $user->id,
            'username_attempted' => $user->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'logout_at' => now(),
            'status' => 'LOGOUT',
        ]);
    }
}
