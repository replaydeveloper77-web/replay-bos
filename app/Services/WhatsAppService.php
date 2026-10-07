<?php

namespace App\Services;

use App\Contracts\WhatsAppProviderInterface;
use App\Models\Ticket;
use App\Models\User;
use App\Models\WhatsAppMessage;
use App\Models\WhatsAppTemplate;
use Illuminate\Http\Request;

class WhatsAppService
{
    public function __construct(
        private WhatsAppProviderInterface $provider,
        private AuditService $auditService,
    ) {
    }

    public function renderTemplate(string $body, Ticket $ticket, ?string $recipientName = null): string
    {
        $replacements = [
            '{{nama}}' => $recipientName ?: ($ticket->customer_name ?: '-'),
            '{{layanan}}' => $ticket->service ?: $ticket->category,
            '{{lokasi}}' => $ticket->location ?: '-',
            '{{ticket_id}}' => $ticket->ticket_number,
            '{{status}}' => $ticket->status,
            '{{waktu_mulai}}' => $ticket->created_at->format('d/m/Y H:i'),
            '{{waktu_resolved}}' => $ticket->resolved_at?->format('d/m/Y H:i') ?? '-',
        ];

        return strtr($body, $replacements);
    }

    public function send(
        Ticket $ticket,
        string $recipientType,
        string $phone,
        ?string $recipientName,
        ?string $templateKey,
        string $message,
        User $actor,
        Request $request,
    ): WhatsAppMessage {
        $whatsappMessage = WhatsAppMessage::create([
            'ticket_id' => $ticket->id,
            'recipient_type' => $recipientType,
            'recipient_name' => $recipientName,
            'phone' => $phone,
            'template_key' => $templateKey,
            'message' => $message,
            'sent_by' => $actor->id,
            'status' => 'PENDING',
            'provider' => $this->provider->name(),
        ]);

        $result = $this->provider->send($phone, $message);

        $whatsappMessage->update([
            'status' => $result['success'] ? 'SENT' : 'FAILED',
            'sent_at' => $result['success'] ? now() : null,
            'provider_message_id' => $result['provider_message_id'],
            'error_message' => $result['error'],
        ]);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'WHATSAPP',
            action: 'SEND_WHATSAPP',
            target: 'WhatsAppMessage',
            targetId: $whatsappMessage->id,
            newValue: [
                'recipient' => $recipientName,
                'phone_masked' => $whatsappMessage->maskedPhone(),
                'template' => $templateKey,
                'result' => $whatsappMessage->status,
            ],
            result: $whatsappMessage->status === 'SENT' ? 'SUCCESS' : 'FAILED',
            ticketId: $ticket->ticket_number,
        );

        return $whatsappMessage->fresh();
    }

    public function updateTemplate(WhatsAppTemplate $template, array $data, User $actor, Request $request): WhatsAppTemplate
    {
        $old = $template->only(['name', 'body', 'is_active']);

        $template->update($data);

        $this->auditService->log(
            actor: $actor,
            request: $request,
            module: 'WHATSAPP',
            action: 'UPDATE_TEMPLATE',
            target: 'WhatsAppTemplate',
            targetId: $template->id,
            oldValue: $old,
            newValue: $template->only(['name', 'body', 'is_active']),
        );

        return $template;
    }
}
