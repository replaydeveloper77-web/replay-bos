<?php

namespace App\Http\Controllers;

use App\Models\Ticket;

class PublicTicketController extends Controller
{
    public function show(string $token)
    {
        $ticket = Ticket::where('public_token', $token)->firstOrFail();

        if ($ticket->isPublicLinkExpired()) {
            return response()->view('public.ticket-expired', [], 410);
        }

        $ticket->load([
            'attachments.uploadedBy',
            'progressUpdates.attachments',
            'progressUpdates.user',
            'customer',
            'team',
            'pic',
            'statusHistories.changedBy',
            'assignments.assignedTo',
            'assignments.assignedTeam',
        ]);

        return view('public.ticket', [
            'ticket' => $ticket,
            'timeline' => $ticket->timeline(),
        ]);
    }
}
