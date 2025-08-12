<?php

namespace App\Services\WhatsappProviders;

use App\Contracts\WhatsappProviderInterface;
use App\Models\WhatsappProvider;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InteraktProvider implements WhatsappProviderInterface
{
    private WhatsappProvider $provider;
    private string $apiKey;
    private string $baseUrl;

    public function __construct(WhatsappProvider $provider)
    {
        $this->provider = $provider;
        $this->apiKey = $provider->getConfig('api_key');
        $this->baseUrl = $provider->api_endpoint; // Use the endpoint from database
    }

    public function sendMessage(string $phoneNumber, string $message, array $options = []): array
    {
        try {
            // Extract country code and phone number
            $countryCode = $options['countryCode'] ?? '+91';
            $cleanPhoneNumber = $this->cleanPhoneNumber($phoneNumber, $countryCode);
            
            $payload = [
                'countryCode' => $countryCode,
                'phoneNumber' => $cleanPhoneNumber,
                'type' => 'Text',
                'data' => [
                    'message' => $message
                ]
            ];

            if (isset($options['callbackData'])) {
                $payload['callbackData'] = $options['callbackData'];
            }
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.'message', $payload);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Interakt send message failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendTemplate(string $phoneNumber, string $templateName, array $parameters = []): array
    {
        try {
            $sendTemplateUrl = $this->baseUrl . 'message/';
            // Extract country code and phone number
            $countryCode = '+91'; // Default to India
            $cleanPhoneNumber = $this->cleanPhoneNumber($phoneNumber, $countryCode);
            
            // If phone number starts with +, extract country code
            if (str_starts_with($phoneNumber, '+')) {
                $countryCode = '+' . substr($phoneNumber, 1, 2); // Extract first 2 digits as country code
                $cleanPhoneNumber = substr($phoneNumber, 3); // Rest as phone number
            }
            $bodyValues = array_values($parameters);
            $payload = [
                'countryCode' => $countryCode,
                'phoneNumber' => $cleanPhoneNumber,
                'type' => 'Template',
                'template' => [
                    'name' => $templateName,
                    'languageCode' => 'en',
                    'bodyValues' => $bodyValues
                ]
            ];

            // Add optional campaign ID if available
            if (isset($this->campaignId)) {
                $payload['campaignId'] = $this->campaignId;
            }

            Log::info('InteraktProvider: Sending template message', [
                'payload' => $payload,
                'endpoint' => $sendTemplateUrl
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($sendTemplateUrl, $payload);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Interakt send template failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendMedia(string $phoneNumber, string $mediaUrl, string $caption = '', string $type = 'image'): array
    {
        try {
            $payload = [
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
            ])->post($this->baseUrl . 'message', $payload);

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Interakt send media failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getMessageStatus(string $messageId): array
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->get($this->baseUrl . "message/{$messageId}");

            return $this->handleResponse($response);
        } catch (\Exception $e) {
            Log::error('Interakt get message status failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function validateWebhook(array $payload): bool
    {
        // Implement webhook validation logic for Interakt
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
                'messageId' => $data['result']['messageId'] ?? $data['messageId'] ?? null,
                'status' => 'sent',
                'provider_response' => $data
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
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . 'track/organization/templates');

            if ($response->successful()) {
                $data = $response->json();
                
                // Extract templates from the nested structure
                $templates = $data['results']['templates'] ?? [];
                
                return [
                    'success' => true,
                    'templates' => $templates,
                    'count' => $data['count'] ?? count($templates),
                    'has_next' => $data['has_next'] ?? false
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
                'status_code' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Interakt get all templates failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get user's message history
     */
    public function getUserMessages(string $phoneNumber, array $options = []): array
    {
        try {
            $countryCode = $options['countryCode'] ?? '+91';
            $cleanPhoneNumber = $this->cleanPhoneNumber($phoneNumber, $countryCode);
            
            $queryParams = [
                'countryCode' => $countryCode,
                'phoneNumber' => $cleanPhoneNumber,
            ];

            // Add optional parameters
            if (isset($options['limit'])) {
                $queryParams['limit'] = $options['limit'];
            }
            if (isset($options['offset'])) {
                $queryParams['offset'] = $options['offset'];
            }
            if (isset($options['startDate'])) {
                $queryParams['startDate'] = $options['startDate'];
            }
            if (isset($options['endDate'])) {
                $queryParams['endDate'] = $options['endDate'];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Basic ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ])->get($this->baseUrl . 'message/', $queryParams);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'messages' => $response->json()
                ];
            }

            return [
                'success' => false,
                'error' => $response->body(),
                'status_code' => $response->status()
            ];
        } catch (\Exception $e) {
            Log::error('Interakt get user messages failed', ['error' => $e->getMessage()]);
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Clean phone number by removing country code if present
     */
    private function cleanPhoneNumber(string $phoneNumber, string $countryCode): string
    {
        // Remove any non-digit characters except +
        $cleanNumber = preg_replace('/[^\d+]/', '', $phoneNumber);
        
        // Remove country code if present at the beginning
        $countryCodeDigits = preg_replace('/[^\d]/', '', $countryCode);
        
        if (str_starts_with($cleanNumber, '+' . $countryCodeDigits)) {
            $cleanNumber = substr($cleanNumber, strlen($countryCodeDigits) + 1);
        } elseif (str_starts_with($cleanNumber, $countryCodeDigits)) {
            $cleanNumber = substr($cleanNumber, strlen($countryCodeDigits));
        }
        
        return $cleanNumber;
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
