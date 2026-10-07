<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditService
{
    public function log(
        ?User $actor,
        Request $request,
        string $module,
        string $action,
        ?string $target = null,
        ?int $targetId = null,
        ?array $oldValue = null,
        ?array $newValue = null,
        string $result = 'SUCCESS',
        ?string $ticketId = null,
        ?string $changeId = null,
    ): AuditLog {
        return AuditLog::create([
            'user_id' => $actor?->id,
            'username' => $actor?->username,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'module' => $module,
            'action' => $action,
            'target' => $target,
            'target_id' => $targetId,
            'old_value' => $oldValue,
            'new_value' => $newValue,
            'ticket_id' => $ticketId,
            'change_id' => $changeId,
            'result' => $result,
        ]);
    }
}
