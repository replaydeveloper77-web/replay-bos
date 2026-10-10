<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\SlaPolicy;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketAssignment;
use App\Models\TicketStatusHistory;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TicketService
{
    public function __construct(private AuditService $auditService)
    {
    }

    public function create(array $data, User $actor, Request $request): Ticket
    {
        return DB::transaction(function () use ($data, $actor, $request) {
            $slaPolicy = SlaPolicy::where('priority', $data['priority'])->first();
            $customerFields = $this->resolveCustomerFields($data);
            $ticketNumber = $this->generateTicketNumber($customerFields['customer_category'] ?? 'HOME');

            $ticket = Ticket::create([
                'ticket_number' => $ticketNumber,
                'public_token' => Str::random(40),
                'title' => $data['title'],
                'description' => $data['description'],
                'category' => $data['category'],
                'subcategory' => $data['subcategory'] ?? null,
                'priority' => $data['priority'],
                'status' => 'OPEN',
                'team_id' => $data['team_id'] ?? null,
                'device' => $data['device'] ?? null,
                'location' => $data['location'] ?? null,
                'service' => $data['service'] ?? null,
                'customer_id' => $customerFields['customer_id'],
                'customer_name' => $customerFields['customer_name'],
                'customer_phone' => $customerFields['customer_phone'],
                'reporter_id' => $data['reporter_id'] ?? $actor->id,
                'created_by' => $actor->id,
                'sla_response_minutes' => $slaPolicy?->response_minutes,
                'sla_due_at' => $slaPolicy ? now()->addMinutes($slaPolicy->response_minutes) : null,
            ]);

            TicketStatusHistory::create([
                'ticket_id' => $ticket->id,
                'from_status' => null,
                'to_status' => 'OPEN',
                'changed_by_id' => $actor->id,
                'note' => 'Ticket dibuat',
                'created_at' => now(),
            ]);

            if (! empty($data['team_id'])) {
                TicketAssignment::create([
                    'ticket_id' => $ticket->id,
                    'assigned_to_id' => null,
                    'assigned_team_id' => $data['team_id'],
                    'assigned_by_id' => $actor->id,
                    'note' => 'Penugasan tim saat tiket dibuat',
                    'created_at' => now(),
                ]);
            }

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'TICKET',
                action: 'CREATE_TICKET',
                target: 'Ticket',
                targetId: $ticket->id,
                newValue: $ticket->only(['title', 'category', 'priority', 'status']),
                ticketId: $ticket->ticket_number,
            );

            return $ticket;
        });
    }

    public function update(Ticket $ticket, array $data, User $actor, Request $request): Ticket
    {
        $editableFields = [
            'title' => 'Judul',
            'description' => 'Deskripsi',
            'category' => 'Category',
            'subcategory' => 'Subcategory',
            'priority' => 'Priority',
            'team_id' => 'Handle By (Tim)',
            'device' => 'Device',
            'location' => 'Location',
            'service' => 'Service',
            'customer_id' => 'Pelanggan',
            'customer_name' => 'Customer/Pelanggan',
            'customer_phone' => 'No. WhatsApp',
            'resolution_note' => 'Catatan Penyelesaian / RFO',
        ];

        $old = $ticket->only(array_keys($editableFields));

        $data = array_merge($data, $this->resolveCustomerFields($data));

        $ticket->update($data);

        if (array_key_exists('team_id', $data) && $data['team_id'] != ($old['team_id'] ?? null)) {
            if ($data['team_id']) {
                TicketAssignment::create([
                    'ticket_id' => $ticket->id,
                    'assigned_to_id' => null,
                    'assigned_team_id' => $data['team_id'],
                    'assigned_by_id' => $actor->id,
                    'note' => 'Update penugasan tim',
                    'created_at' => now(),
                ]);
            }
        }

        $new = $ticket->only(array_keys($editableFields));

        $changedFields = array_filter(
            array_keys($editableFields),
            fn ($field) => ($old[$field] ?? null) !== ($new[$field] ?? null),
        );

        if (empty($changedFields)) {
            return $ticket;
        }

        $summary = collect($changedFields)
            ->map(fn ($field) => "{$editableFields[$field]}: '".($old[$field] ?? '-')."' -> '".($new[$field] ?? '-')."'")
            ->implode('; ');

        TicketStatusHistory::create([
            'ticket_id' => $ticket->id,
            'from_status' => $ticket->status,
            'to_status' => $ticket->status,
            'changed_by_id' => $actor->id,
            'note' => "Edit detail ticket — {$summary}",
            'created_at' => now(),
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'TICKET',
            action: 'UPDATE_TICKET_DETAILS',
            target: 'Ticket',
            targetId: $ticket->id,
            oldValue: array_intersect_key($old, array_flip($changedFields)),
            newValue: array_intersect_key($new, array_flip($changedFields)),
            ticketId: $ticket->ticket_number,
        );

        return $ticket;
    }

    public function changeStatus(Ticket $ticket, string $toStatus, ?string $note, ?string $escalationLevel, User $actor, Request $request): Ticket
    {
        $fromStatus = $ticket->status;

        if (in_array($toStatus, Ticket::CLOSED_STATUSES, true) && blank($note)) {
            throw new \InvalidArgumentException('Catatan penyelesaian wajib diisi untuk status RESOLVED/CLOSED.');
        }

        if ($toStatus === 'ESCALATED' && (blank($note) || blank($escalationLevel))) {
            throw new \InvalidArgumentException('Level eskalasi dan alasan eskalasi wajib diisi.');
        }

        $updates = ['status' => $toStatus];

        if ($toStatus === 'ACKNOWLEDGED' && ! $ticket->acknowledged_at) {
            $updates['acknowledged_at'] = now();
        }
        if ($toStatus === 'ESCALATED') {
            $updates['escalation_level'] = $escalationLevel;
        }
        if ($toStatus === 'RESOLVED') {
            $updates['resolved_at'] = now();
            $updates['resolution_note'] = $note;
        }
        if ($toStatus === 'CLOSED') {
            $updates['closed_at'] = now();
            if ($note) {
                $updates['resolution_note'] = $note;
            }
        }

        $ticket->update($updates);

        $historyNote = $toStatus === 'ESCALATED' ? "[Eskalasi ke {$escalationLevel}] {$note}" : $note;

        TicketStatusHistory::create([
            'ticket_id' => $ticket->id,
            'from_status' => $fromStatus,
            'to_status' => $toStatus,
            'changed_by_id' => $actor->id,
            'note' => $historyNote,
            'created_at' => now(),
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'TICKET',
            action: $toStatus === 'ESCALATED' ? 'ESCALATE_TICKET' : 'UPDATE_TICKET_STATUS',
            target: 'Ticket',
            targetId: $ticket->id,
            oldValue: ['status' => $fromStatus],
            newValue: ['status' => $toStatus, 'escalation_level' => $escalationLevel],
            ticketId: $ticket->ticket_number,
        );

        return $ticket->fresh();
    }

    public function assign(Ticket $ticket, User $assignee, ?string $note, User $actor, Request $request): Ticket
    {
        $oldPicId = $ticket->pic_id;

        $ticket->update(['pic_id' => $assignee->id]);

        TicketAssignment::create([
            'ticket_id' => $ticket->id,
            'assigned_to_id' => $assignee->id,
            'assigned_by_id' => $actor->id,
            'note' => $note,
            'created_at' => now(),
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'TICKET',
            action: 'ASSIGN_TICKET',
            target: 'Ticket',
            targetId: $ticket->id,
            oldValue: ['pic_id' => $oldPicId],
            newValue: ['pic_id' => $assignee->id, 'pic_username' => $assignee->username],
            ticketId: $ticket->ticket_number,
        );

        return $ticket->fresh();
    }

    public function assignTeam(Ticket $ticket, Team $team, ?string $note, User $actor, Request $request): Ticket
    {
        $oldTeamId = $ticket->team_id;

        $ticket->update(['team_id' => $team->id]);

        TicketAssignment::create([
            'ticket_id' => $ticket->id,
            'assigned_team_id' => $team->id,
            'assigned_by_id' => $actor->id,
            'note' => $note,
            'created_at' => now(),
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'TICKET',
            action: 'ASSIGN_TEAM',
            target: 'Ticket',
            targetId: $ticket->id,
            oldValue: ['team_id' => $oldTeamId],
            newValue: ['team_id' => $team->id, 'team_name' => $team->name],
            ticketId: $ticket->ticket_number,
        );

        return $ticket->fresh();
    }

    private function generateTicketNumber(?string $category = 'HOME'): string
    {
        $prefix = Ticket::prefixForCategory($category) . now()->format('Ymd') . '-';

        // Based on the highest number already issued with this prefix for today, not a row count —
        // a row count would reuse numbers if an earlier ticket is ever deleted.
        $lastNumber = Ticket::where('ticket_number', 'like', $prefix.'%')
            ->lockForUpdate()
            ->orderByRaw('LENGTH(ticket_number) DESC, ticket_number DESC')
            ->value('ticket_number');

        $nextSeq = 1;
        if ($lastNumber && preg_match('/^'.preg_quote($prefix, '/').'(\d+)$/', $lastNumber, $matches)) {
            $nextSeq = ((int) $matches[1]) + 1;
        }

        return $prefix.str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * If a customer_id (Pelanggan) was picked, snapshot its name/whatsapp onto the ticket —
     * this overrides any manually-typed customer_name/customer_phone. Free-text stays
     * available for ad-hoc customers not yet in the Pelanggan directory.
     */
    private function resolveCustomerFields(array $data): array
    {
        if (! empty($data['customer_id'])) {
            $customer = Customer::find($data['customer_id']);

            if ($customer) {
                return [
                    'customer_id' => $customer->id,
                    'customer_name' => $customer->name,
                    'customer_phone' => $customer->whatsapp,
                    'customer_category' => $data['customer_category'] ?? ($customer->category ?? 'HOME'),
                ];
            }
        }

        return [
            'customer_id' => null,
            'customer_name' => $data['customer_name'] ?? null,
            'customer_phone' => $data['customer_phone'] ?? null,
            'customer_category' => $data['customer_category'] ?? 'HOME',
        ];
    }

    public function delete(Ticket $ticket, User $actor, Request $request): void
    {
        DB::transaction(function () use ($ticket, $actor, $request) {
            foreach ($ticket->attachments as $attachment) {
                if ($attachment->storage_path && Storage::disk('public')->exists($attachment->storage_path)) {
                    Storage::disk('public')->delete($attachment->storage_path);
                }
                $attachment->delete();
            }

            foreach ($ticket->troubleshootingSessions as $session) {
                $session->steps()->delete();
                $session->delete();
            }

            $ticket->statusHistories()->delete();
            $ticket->assignments()->delete();
            $ticket->whatsAppMessages()->delete();

            $oldData = $ticket->only(['id', 'ticket_number', 'title', 'status', 'category']);

            $ticket->delete();

            $this->auditService->log(
                actor: $actor,
                request: $request,
                module: 'TICKET',
                action: 'DELETE_TICKET',
                target: 'Ticket',
                targetId: $oldData['id'] ?? null,
                oldValue: $oldData,
                ticketId: $oldData['ticket_number'] ?? null,
            );
        });
    }
}
