<?php

namespace App\Services\WhatsappProviders;

use App\Contracts\WhatsappProviderInterface;
use App\Models\WhatsappProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GallaboxProvider implements WhatsappProviderInterface
{
    private WhatsappProvider $provider;
    private string $apiKey;
    private string $workspaceId;
    private string $baseUrl;

    public function __construct(WhatsappProvider $provider)
    {
        $this->provider = $provider;
        $this->apiKey = $provider->getConfig('api_key');
        $this->workspaceId = $provider->getConfig('workspace_id');
        $this->baseUrl = $provider->api_endpoint;
    }

    public function sendMessage(string $phoneNumber, string $message, array $options = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . 'messages', [
                'workspace_id' => $this->workspaceId,
                'to' => $phoneNumber,
                'type' => 'text',
                'text' => ['body' => $message],
            ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Gallabox send message failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendTemplate(string $phoneNumber, string $templateName, array $parameters = []): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . 'messages', [
                'workspace_id' => $this->workspaceId,
                'to' => $phoneNumber,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => ['code' => 'en'],
                    'components' => $this->formatTemplateComponents($parameters)
                ],
            ]);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Gallabox send template failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendMedia(string $phoneNumber, string $mediaUrl, string $caption = '', string $type = 'image'): array
    {
        try {
            $payload = [
                'workspace_id' => $this->workspaceId,
                'to' => $phoneNumber,
                'type' => $type,
                $type => ['link' => $mediaUrl]
            ];

            if ($caption) {
                $payload[$type]['caption'] = $caption;
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl . 'messages', $payload);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Gallabox send media failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getMessageStatus(string $messageId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . "messages/{$messageId}");

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Gallabox get message status failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function validateWebhook(array $payload): bool
    {
        // Implement webhook validation logic for Gallabox
        return true;
    }

    public function isHealthy(): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . 'health');

            return $response->successful();
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
                'message_id' => $data['id'] ?? null,
                'response' => $data
            ];
        }

        return [
            'success' => false,
            'error' => $response->body(),
            'status_code' => $response->status()
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

    /**
     * Get all available templates
     */
    public function getAllTemplates(): array
    {
        // Gallabox doesn't have a direct API for getting all templates
        // This would need to be implemented based on Gallabox's API documentation
        return [
            'success' => false,
            'error' => 'Get all templates not implemented for Gallabox provider'
        ];
    }

    /**
     * Get user messages
     */
    public function getUserMessages(string $phoneNumber, array $options = []): array
    {
        // Gallabox doesn't have a direct API for getting user messages
        // This would need to be implemented based on Gallabox's API documentation
        return [
            'success' => false,
            'error' => 'Get user messages not implemented for Gallabox provider'
        ];
    }
}
