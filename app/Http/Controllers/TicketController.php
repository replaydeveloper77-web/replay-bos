<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignTeamRequest;
use App\Http\Requests\AssignTicketRequest;
use App\Http\Requests\ChangeTicketStatusRequest;
use App\Http\Requests\SendWhatsAppMessageRequest;
use App\Http\Requests\StoreTicketAttachmentRequest;
use App\Http\Requests\StoreTicketRequest;
use App\Http\Requests\UpdateTicketRequest;
use App\Models\Customer;
use App\Models\Team;
use App\Models\Ticket;
use App\Models\TicketAttachment;
use App\Models\TicketTitle;
use App\Models\User;
use App\Models\WhatsAppTemplate;
use App\Services\AuditService;
use App\Services\TicketService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TicketController extends Controller
{
    public function __construct(
        private TicketService $ticketService,
        private AuditService $auditService,
        private WhatsAppService $whatsAppService,
    ) {
    }

    public function index(Request $request)
    {
        $this->authorize('viewAny', Ticket::class);

        $customerCategory = $request->string('customer_category')->trim();
        $search = $request->string('search')->trim();

        $corporateCount = Ticket::forCustomerCategory('CORPORATE')->count();
        $resellerCount = Ticket::forCustomerCategory('RESELLER')->count();
        $homeCount = Ticket::forCustomerCategory('HOME')->count();
        $handleCount = Ticket::where('status', 'HANDLE')->count();
        $closedCount = Ticket::where('status', 'CLOSED')->count();
        $totalCount = Ticket::count();

        $tickets = Ticket::with(['pic', 'reporter', 'team', 'customer'])
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')))
            ->when($request->filled('priority'), fn ($q) => $q->where('priority', $request->string('priority')))
            ->when($request->filled('category'), fn ($q) => $q->where('category', $request->string('category')))
            ->when($customerCategory->isNotEmpty(), fn ($q) => $q->forCustomerCategory($customerCategory))
            ->when($request->filled('date'), function ($q) use ($request) {
                $q->whereDate('created_at', $request->string('date'));
            }, function ($q) use ($request) {
                $q->when($request->filled('month'), fn ($sub) => $sub->whereMonth('created_at', $request->integer('month')))
                  ->when($request->filled('year'), fn ($sub) => $sub->whereYear('created_at', $request->integer('year')));
            })
            ->when($search->isNotEmpty(), function ($q) use ($search) {
                $term = "%{$search}%";
                $q->where(function ($sub) use ($term) {
                    $sub->where('ticket_number', 'like', $term)
                        ->orWhere('title', 'like', $term)
                        ->orWhere('category', 'like', $term)
                        ->orWhere('subcategory', 'like', $term)
                        ->orWhere('customer_name', 'like', $term)
                        ->orWhereHas('customer', function ($c) use ($term) {
                            $c->where('name', 'like', $term)
                              ->orWhere('company', 'like', $term)
                              ->orWhere('category', 'like', $term);
                        });
                });
            })
            ->latest('created_at')
            ->paginate(10)
            ->withQueryString();

        $availableYears = Ticket::selectRaw('YEAR(created_at) as y')
            ->distinct()
            ->orderByDesc('y')
            ->pluck('y')
            ->filter()
            ->map(fn ($y) => (int) $y)
            ->all();

        $curYear = (int) date('Y');
        if (!in_array($curYear, $availableYears)) {
            array_unshift($availableYears, $curYear);
        }

        return view('tickets.index', [
            'tickets' => $tickets,
            'statuses' => Ticket::STATUSES,
            'priorities' => Ticket::PRIORITIES,
            'categories' => Ticket::CATEGORIES,
            'corporateCount' => $corporateCount,
            'resellerCount' => $resellerCount,
            'homeCount' => $homeCount,
            'handleCount' => $handleCount,
            'closedCount' => $closedCount,
            'totalCount' => $totalCount,
            'availableYears' => $availableYears,
        ]);
    }

    public function create()
    {
        $this->authorize('create', Ticket::class);

        return view('tickets.create', [
            'priorities' => Ticket::PRIORITIES,
            'categories' => Ticket::CATEGORIES,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'teams' => Team::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function store(StoreTicketRequest $request)
    {
        $ticket = $this->ticketService->create($request->validated(), $request->user(), $request);

        return redirect()->route('tickets.show', $ticket)->with('status', "Ticket {$ticket->ticket_number} berhasil dibuat.");
    }

    public function show(Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load([
            'pic', 'reporter', 'creator', 'team', 'statusHistories.changedBy',
            'assignments.assignedTo', 'assignments.assignedBy', 'assignments.assignedTeam',
            'troubleshootingSessions.createdBy', 'troubleshootingSessions.steps.createdBy',
            'attachments.uploadedBy', 'whatsAppMessages.sentBy',
        ]);
        $assignableUsers = User::where('is_active', true)->orderBy('name')->get();
        $assignableTeams = Team::where('is_active', true)->orderBy('name')->get();

        $deviceHistory = collect();
        if (filled($ticket->device)) {
            $deviceHistory = Ticket::where('device', $ticket->device)
                ->where('id', '!=', $ticket->id)
                ->latest('created_at')
                ->limit(10)
                ->get();
        }

        return view('tickets.show', [
            'ticket' => $ticket,
            'timeline' => $ticket->timeline(),
            'whatsappText' => $ticket->whatsappSummary(),
            'deviceHistory' => $deviceHistory,
            'assignableUsers' => $assignableUsers,
            'assignableTeams' => $assignableTeams,
            'statuses' => Ticket::STATUSES,
            'escalationLevels' => Ticket::ESCALATION_LEVELS,
            'tsCategories' => \App\Models\TroubleshootingSession::CATEGORIES,
            'physicalChecklist' => \App\Models\TroubleshootingSession::PHYSICAL_CHECKLIST,
            'logicChecklist' => \App\Models\TroubleshootingSession::LOGIC_CHECKLIST,
            'stepTypes' => \App\Models\TroubleshootingStep::STEP_TYPES,
            'whatsappTemplates' => WhatsAppTemplate::where('is_active', true)->get(),
        ]);
    }

    public function edit(Ticket $ticket)
    {
        $this->authorize('update', $ticket);

        return view('tickets.edit', [
            'ticket' => $ticket,
            'priorities' => Ticket::PRIORITIES,
            'categories' => Ticket::CATEGORIES,
            'customers' => Customer::where('is_active', true)->orderBy('name')->get(),
            'teams' => Team::where('is_active', true)->orderBy('name')->get(),
        ]);
    }

    public function update(UpdateTicketRequest $request, Ticket $ticket)
    {
        $this->ticketService->update($ticket, $request->validated(), $request->user(), $request);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket berhasil diperbarui.');
    }

    public function destroy(Request $request, Ticket $ticket)
    {
        $this->authorize('delete', $ticket);

        $ticketNumber = $ticket->ticket_number;
        $this->ticketService->delete($ticket, $request->user(), $request);

        return redirect()->route('tickets.index')->with('status', "Ticket {$ticketNumber} berhasil dihapus.");
    }

    public function changeStatus(ChangeTicketStatusRequest $request, Ticket $ticket)
    {
        try {
            $this->ticketService->changeStatus(
                $ticket,
                $request->validated()['status'],
                $request->validated()['note'] ?? null,
                $request->validated()['escalation_level'] ?? null,
                $request->user(),
                $request,
            );
        } catch (\InvalidArgumentException $e) {
            return back()->withErrors(['note' => $e->getMessage()]);
        }

        return redirect()->route('tickets.show', $ticket)->with('status', 'Status ticket berhasil diperbarui.');
    }

    public function assign(AssignTicketRequest $request, Ticket $ticket)
    {
        $assignee = User::findOrFail($request->validated()['assigned_to_id']);

        $this->ticketService->assign($ticket, $assignee, $request->validated()['note'] ?? null, $request->user(), $request);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket berhasil di-assign.');
    }

    public function assignTeam(AssignTeamRequest $request, Ticket $ticket)
    {
        $team = Team::findOrFail($request->validated()['team_id']);

        $this->ticketService->assignTeam($ticket, $team, $request->validated()['note'] ?? null, $request->user(), $request);

        return redirect()->route('tickets.show', $ticket)->with('status', 'Ticket berhasil di-assign ke tim.');
    }

    public function shareWhatsapp(Request $request, Ticket $ticket)
    {
        $this->authorize('view', $ticket);

        $ticket->load(['statusHistories.changedBy', 'assignments.assignedTo', 'assignments.assignedBy', 'attachments']);

        $this->auditService->log(
            actor: $request->user(),
            request: $request,
            module: 'TICKET',
            action: 'SHARE_WHATSAPP_TIMELINE',
            target: 'Ticket',
            targetId: $ticket->id,
            ticketId: $ticket->ticket_number,
        );

        $phone = $ticket->whatsappPhoneNumber();
        $text = rawurlencode($ticket->whatsappSummary());
        $url = $phone ? "https://wa.me/{$phone}?text={$text}" : "https://wa.me/?text={$text}";

        return redirect()->away($url);
    }

    public function sendWhatsappMessage(SendWhatsAppMessageRequest $request, Ticket $ticket)
    {
        $data = $request->validated();

        $phone = $data['recipient_type'] === 'CUSTOMER'
            ? $ticket->whatsappPhoneNumber()
            : preg_replace('/\D/', '', $data['phone'] ?? '');

        if (blank($phone)) {
            return back()->withErrors(['phone' => 'Nomor WhatsApp tujuan tidak ditemukan/kosong.'])->withInput();
        }

        $recipientName = ($data['recipient_name'] ?? null) ?: ($data['recipient_type'] === 'CUSTOMER' ? $ticket->customer_name : null);

        $message = $this->whatsAppService->send(
            ticket: $ticket,
            recipientType: $data['recipient_type'],
            phone: $phone,
            recipientName: $recipientName,
            templateKey: $data['template_key'] ?? null,
            message: $data['message'],
            actor: $request->user(),
            request: $request,
        );

        $status = $message->status === 'SENT'
            ? 'Pesan WhatsApp berhasil dikirim.'
            : "Pengiriman WhatsApp gagal: {$message->error_message}";

        return redirect()->route('tickets.show', $ticket)->with($message->status === 'SENT' ? 'status' : 'wa_error', $status);
    }

    public function storeAttachment(StoreTicketAttachmentRequest $request, Ticket $ticket)
    {
        foreach ($request->file('files') as $file) {
            $path = $file->store("ticket-attachments/{$ticket->id}", 'public');

            $attachment = TicketAttachment::create([
                'ticket_id' => $ticket->id,
                'uploaded_by' => $request->user()->id,
                'disk_path' => $path,
                'original_filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType(),
                'file_size' => $file->getSize(),
            ]);

            $this->auditService->log(
                actor: $request->user(),
                request: $request,
                module: 'TICKET',
                action: 'ADD_ATTACHMENT',
                target: 'TicketAttachment',
                targetId: $attachment->id,
                newValue: ['filename' => $attachment->original_filename],
                ticketId: $ticket->ticket_number,
            );
        }

        return redirect()->route('tickets.show', $ticket)->with('status', 'Lampiran berhasil diupload.');
    }

    public function destroyAttachment(Request $request, Ticket $ticket, TicketAttachment $attachment)
    {
        $this->authorize('update', $ticket);
        abort_unless($attachment->ticket_id === $ticket->id, 404);

        Storage::disk('public')->delete($attachment->disk_path);

        $this->auditService->log(
            actor: $request->user(),
            request: $request,
            module: 'TICKET',
            action: 'DELETE_ATTACHMENT',
            target: 'TicketAttachment',
            targetId: $attachment->id,
            oldValue: ['filename' => $attachment->original_filename],
            ticketId: $ticket->ticket_number,
        );

        $attachment->delete();

        return redirect()->route('tickets.show', $ticket)->with('status', 'Lampiran berhasil dihapus.');
    }
}
