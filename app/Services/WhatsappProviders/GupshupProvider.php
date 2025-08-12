<?php

namespace App\Services\WhatsappProviders;

use App\Contracts\WhatsappProviderInterface;
use App\Models\WhatsappProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GupshupProvider implements WhatsappProviderInterface
{
    private WhatsappProvider $provider;
    private string $apiKey;
    private string $appName;
    private string $baseUrl;

    public function __construct(WhatsappProvider $provider)
    {
        $this->provider = $provider;
        $this->apiKey = $provider->getConfig('api_key');
        $this->appName = $provider->getConfig('app_name');
        $this->baseUrl = $provider->api_endpoint;
    }

    public function sendMessage(string $phoneNumber, string $message, array $options = []): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($this->baseUrl . 'msg', [
                'channel' => 'whatsapp',
                'source' => $this->appName,
                'destination' => $phoneNumber,
                'message' => json_encode(['type' => 'text', 'text' => $message]),
            ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Gupshup send message failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendTemplate(string $phoneNumber, string $templateName, array $parameters = []): array
    {
        try {
            $templateMessage = [
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => 'en'],
                    'components' => $this->formatTemplateComponents($parameters)
                ]
            ];

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($this->baseUrl . 'msg', [
                'channel' => 'whatsapp',
                'source' => $this->appName,
                'destination' => $phoneNumber,
                'message' => json_encode($templateMessage),
            ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Gupshup send template failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendMedia(string $phoneNumber, string $mediaUrl, string $caption = '', string $type = 'image'): array
    {
        try {
            $mediaMessage = [
                'type' => $type,
                $type => ['link' => $mediaUrl]
            ];

            if ($caption) {
                $mediaMessage[$type]['caption'] = $caption;
            }

            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->asForm()->post($this->baseUrl . 'msg', [
                'channel' => 'whatsapp',
                'source' => $this->appName,
                'destination' => $phoneNumber,
                'message' => json_encode($mediaMessage),
            ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Gupshup send media failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getMessageStatus(string $messageId): array
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->get($this->baseUrl . "msg/{$messageId}");

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Gupshup get message status failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function validateWebhook(array $payload): bool
    {
        // Implement webhook validation logic for Gupshup
        return true;
    }

    public function isHealthy(): bool
    {
        try {
            $response = Http::withHeaders([
                'apikey' => $this->apiKey,
            ])->get($this->baseUrl . 'health');

            return $response->status() !== 401; // Not unauthorized
        } catch (\Exception $e) {
            return false;
        }
    }

    private function handleResponse($response): array
    {
        if ($response->successful()) {
            $data = $response->json();
            return [
                'success' => true,
                'message_id' => $data['messageId'] ?? null,
                'response' => $data
            ];
        }

        return [
            'success' => false,
            'error' => $response->body(),
            'status_code' => $response->status()
        ];
    }

    /**
     * Get all available templates
     */
    public function getAllTemplates(): array
    {
        // Gupshup doesn't have a direct API for getting all templates
        // This would need to be implemented based on Gupshup's API documentation
        return [
            'success' => false,
            'error' => 'Get all templates not implemented for Gupshup provider'
        ];
    }

    /**
     * Get user messages
     */
    public function getUserMessages(string $phoneNumber, array $options = []): array
    {
        // Gupshup doesn't have a direct API for getting user messages
        // This would need to be implemented based on Gupshup's API documentation
        return [
            'success' => false,
            'error' => 'Get user messages not implemented for Gupshup provider'
        ];
    }

    private function formatTemplateComponents(array $parameters): array
    {
        return [
            [
                'type' => 'body',
                'parameters' => array_map(fn($param) => ['type' => 'text', 'text' => $param], $parameters)
            ]
        ];
    }
}
