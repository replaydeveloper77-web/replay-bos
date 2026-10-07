<?php

namespace App\Services\WhatsAppProviders;

use App\Contracts\WhatsAppProviderInterface;
use Illuminate\Support\Facades\Http;

class FonnteProvider implements WhatsAppProviderInterface
{
    public function __construct(
        private ?string $token = null,
        private string $endpoint = 'https://api.fonnte.com/send',
    ) {
        $this->token ??= config('services.fonnte.token');
    }

    public function name(): string
    {
        return 'fonnte';
    }

    public function send(string $phone, string $message): array
    {
        if (blank($this->token)) {
            return [
                'success' => false,
                'provider_message_id' => null,
                'error' => 'Fonnte API token belum dikonfigurasi (isi FONNTE_TOKEN di .env).',
            ];
        }

        try {
            $response = Http::asForm()
                ->withHeaders(['Authorization' => $this->token])
                ->timeout(15)
                ->post($this->endpoint, [
                    'target' => $phone,
                    'message' => $message,
                ]);

            $body = $response->json();

            if ($response->successful() && ($body['status'] ?? false)) {
                return [
                    'success' => true,
                    'provider_message_id' => $body['id'][0] ?? null,
                    'error' => null,
                ];
            }

            return [
                'success' => false,
                'provider_message_id' => null,
                'error' => $body['reason'] ?? $body['detail'] ?? 'Fonnte API menolak permintaan (HTTP '.$response->status().').',
            ];
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'provider_message_id' => null,
                'error' => 'Gagal menghubungi Fonnte: '.$e->getMessage(),
            ];
        }
    }
}
