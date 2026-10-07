<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TroubleshootingSession;
use App\Models\TroubleshootingStep;
use App\Models\User;
use Illuminate\Http\Request;

class TroubleshootingService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function startSession(Ticket $ticket, array $data, User $actor, Request $request): TroubleshootingSession
    {
        $session = TroubleshootingSession::create([
            'ticket_id' => $ticket->id,
            'category' => $data['category'],
            'physical_checklist' => $data['physical_checklist'] ?? null,
            'logic_checklist' => $data['logic_checklist'] ?? null,
            'hypothesis' => $data['hypothesis'] ?? null,
            'status' => 'OPEN',
            'created_by' => $actor->id,
        ]);

        TroubleshootingStep::create([
            'session_id' => $session->id,
            'step_type' => 'OBSERVATION',
            'description' => $data['observation'],
            'created_by' => $actor->id,
            'created_at' => now(),
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'TROUBLESHOOTING',
            action: 'START_SESSION',
            target: 'TroubleshootingSession',
            targetId: $session->id,
            newValue: ['category' => $session->category, 'ticket_id' => $ticket->id],
            ticketId: $ticket->ticket_number,
        );

        return $session;
    }

    public function addStep(TroubleshootingSession $session, string $stepType, string $description, User $actor, Request $request): TroubleshootingStep
    {
        $step = TroubleshootingStep::create([
            'session_id' => $session->id,
            'step_type' => $stepType,
            'description' => $description,
            'created_by' => $actor->id,
            'created_at' => now(),
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'TROUBLESHOOTING',
            action: 'ADD_STEP',
            target: 'TroubleshootingSession',
            targetId: $session->id,
            newValue: ['step_type' => $stepType],
            ticketId: $session->ticket->ticket_number,
        );

        return $step;
    }

    public function closeSession(TroubleshootingSession $session, string $conclusion, User $actor, Request $request): TroubleshootingSession
    {
        $session->update([
            'status' => 'CLOSED',
            'conclusion' => $conclusion,
        ]);

        TroubleshootingStep::create([
            'session_id' => $session->id,
            'step_type' => 'DOCUMENTATION',
            'description' => $conclusion,
            'created_by' => $actor->id,
            'created_at' => now(),
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'TROUBLESHOOTING',
            action: 'CLOSE_SESSION',
            target: 'TroubleshootingSession',
            targetId: $session->id,
            ticketId: $session->ticket->ticket_number,
        );

        return $session->fresh();
    }
}
