<?php

namespace App\Services;

use App\Models\WhatsappProvider;
use App\Models\WhatsappMessage;
use App\Contracts\WhatsappProviderInterface;
use App\Services\WhatsappProviderFactory;
use Illuminate\Support\Facades\Log;

class WhatsappService
{
    private ?WhatsappProviderInterface $currentProvider = null;
    private ?WhatsappProvider $providerModel = null;

    public function selectProvider(?string $providerSlug = null): self
    {
        if ($providerSlug) {
            $this->providerModel = WhatsappProvider::where('slug', $providerSlug)
                ->where('is_active', true)
                ->first();
        } else {
            // Auto-select the highest priority active provider
            $this->providerModel = WhatsappProvider::active()
                ->orderBy('priority', 'asc')
                ->first();
        }

        if (!$this->providerModel) {
            throw new \Exception('No active WhatsApp provider found');
        }

        $this->currentProvider = WhatsappProviderFactory::create($this->providerModel);
        
        return $this;
    }

    public function sendMessage(string $phoneNumber, string $message, array $options = []): array
    {
        if (!$this->currentProvider) {
            $this->selectProvider();
        }

        // Create message record
        $messageRecord = WhatsappMessage::create([
            'phone_number' => $phoneNumber,
            'message_type' => 'text',
            'message_content' => $message,
            'status' => 'pending',
        ]);

        try {
            $result = $this->currentProvider->sendMessage($phoneNumber, $message, $options);
            
            // Update message record
            $messageRecord->update([
                'status' => $result['success'] ? 'sent' : 'failed',
                'message_id' => $result['message_id'] ?? null,
                'provider_response' => $result,
                'error_message' => $result['success'] ? null : ($result['error'] ?? 'Unknown error'),
                'sent_at' => $result['success'] ? now() : null,
            ]);

            return $result;
        } catch (\Exception $e) {
            $messageRecord->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('WhatsApp message sending failed', [
                'provider' => $this->providerModel->slug,
                'phone' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendTemplate(string $phoneNumber, string $templateName, array $parameters = []): array
    {
        if (!$this->currentProvider) {
            $this->selectProvider();
        }

        $messageRecord = WhatsappMessage::create([
            'phone_number' => $phoneNumber,
            'message_type' => 'template',
            'template_name' => $templateName,
            'template_variables' => json_encode($parameters),
            'status' => 'pending',
        ]);

        try {
            $result = $this->currentProvider->sendTemplate($phoneNumber, $templateName, $parameters);
            
            // Update message record
            $messageRecord->update([
                'status' => $result['success'] ? 'sent' : 'failed',
                'message_id' => $result['message_id'] ?? null,
                'provider_response' => $result,
                'error_message' => $result['success'] ? null : ($result['error'] ?? 'Unknown error'),
                'sent_at' => $result['success'] ? now() : null,
            ]);

            return $result;
        } catch (\Exception $e) {
            $messageRecord->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('WhatsApp template sending failed', [
                'provider' => $this->providerModel->slug,
                'phone' => $phoneNumber,
                'template' => $templateName,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function sendMedia(string $phoneNumber, string $mediaUrl, string $caption = '', string $type = 'image'): array
    {
        if (!$this->currentProvider) {
            $this->selectProvider();
        }

        $messageRecord = WhatsappMessage::create([
            'phone_number' => $phoneNumber,
            'message_type' => $type,
            'message_content' => json_encode(['media_url' => $mediaUrl, 'caption' => $caption]),
            'status' => 'pending',
        ]);

        try {
            $result = $this->currentProvider->sendMedia($phoneNumber, $mediaUrl, $caption, $type);
            
            // Update message record
            $messageRecord->update([
                'status' => $result['success'] ? 'sent' : 'failed',
                'message_id' => $result['message_id'] ?? null,
                'provider_response' => $result,
                'error_message' => $result['success'] ? null : ($result['error'] ?? 'Unknown error'),
                'sent_at' => $result['success'] ? now() : null,
            ]);

            return $result;
        } catch (\Exception $e) {
            $messageRecord->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            Log::error('WhatsApp media sending failed', [
                'provider' => $this->providerModel->slug,
                'phone' => $phoneNumber,
                'media_url' => $mediaUrl,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function getAvailableProviders(): \Illuminate\Database\Eloquent\Collection
    {
        return WhatsappProvider::where('is_active', true)
            ->orderBy('priority', 'asc')
            ->get();
    }

    public function getCurrentProvider(): ?WhatsappProvider
    {
        return $this->providerModel;
    }

    public function switchToFallbackProvider(): bool
    {
        if (!$this->providerModel) {
            return false;
        }

        $nextProvider = WhatsappProvider::where('is_active', true)
            ->where('priority', '>', $this->providerModel->priority)
            ->orderBy('priority', 'asc')
            ->first();

        if ($nextProvider) {
            $this->selectProvider($nextProvider->slug);
            return true;
        }

        return false;
    }

    public function getMessageStatus(string $messageId): array
    {
        if (!$this->currentProvider) {
            return ['success' => false, 'error' => 'No provider selected'];
        }

        return $this->currentProvider->getMessageStatus($messageId);
    }

    public function isProviderHealthy(?string $providerSlug = null): bool
    {
        if ($providerSlug) {
            $provider = WhatsappProvider::where('slug', $providerSlug)->first();
            if (!$provider) {
                return false;
            }
            $providerInstance = WhatsappProviderFactory::create($provider);
        } else {
            if (!$this->currentProvider) {
                $this->selectProvider();
            }
            $providerInstance = $this->currentProvider;
        }

        return $providerInstance->isHealthy();
    }

    /**
     * Get all templates from the current provider
     */
    public function getAllTemplates(?string $providerSlug = null): array
    {
        try {
            if ($providerSlug) {
                $this->selectProvider($providerSlug);
            } elseif (!$this->currentProvider) {
                $this->selectProvider();
            }

            return $this->currentProvider->getAllTemplates();
        } catch (\Exception $e) {
            Log::error('WhatsApp get all templates failed', [
                'provider' => $this->providerModel?->slug,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get user messages from the current provider
     */
    public function getUserMessages(string $phoneNumber, array $options = [], ?string $providerSlug = null): array
    {
        try {
            if ($providerSlug) {
                $this->selectProvider($providerSlug);
            } elseif (!$this->currentProvider) {
                $this->selectProvider();
            }

            return $this->currentProvider->getUserMessages($phoneNumber, $options);
        } catch (\Exception $e) {
            Log::error('WhatsApp get user messages failed', [
                'provider' => $this->providerModel?->slug,
                'phone_number' => $phoneNumber,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send message using existing WhatsappMessage record (for automation jobs)
     */
    public function sendExistingMessage(WhatsappMessage $message): array
    {
        if (!$this->currentProvider) {
            $this->selectProvider();
        }

        try {
            $result = match ($message->message_type) {
                'text' => $this->currentProvider->sendMessage(
                    $message->phone_number, 
                    $message->message_content
                ),
                'template' => $this->sendExistingTemplate($message),
                default => throw new \InvalidArgumentException("Unsupported message type: {$message->message_type}")
            };

            return $result;
        } catch (\Exception $e) {
            Log::error('WhatsApp existing message sending failed', [
                'provider' => $this->providerModel->slug,
                'message_id' => $message->id,
                'phone' => $message->phone_number,
                'error' => $e->getMessage()
            ]);

            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send template using existing WhatsappMessage record
     */
    private function sendExistingTemplate(WhatsappMessage $message): array
    {
        $templateName = $message->template_name;
        $parameters = $message->template_variables ?? [];

        return $this->currentProvider->sendTemplate(
            $message->phone_number, 
            $templateName, 
            $parameters
        );
    }
}
