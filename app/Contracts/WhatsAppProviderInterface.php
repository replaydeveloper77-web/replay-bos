<?php

namespace App\Contracts;

interface WhatsAppProviderInterface
{
    /**
     * @return array{success: bool, provider_message_id: ?string, error: ?string}
     */
    public function send(string $phone, string $message): array;

    public function name(): string;
}
