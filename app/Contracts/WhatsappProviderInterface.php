<?php

namespace App\Contracts;

interface WhatsappProviderInterface
{
    public function sendMessage(string $phoneNumber, string $message, array $options = []): array;
    public function sendTemplate(string $phoneNumber, string $templateName, array $parameters = []): array;
    public function sendMedia(string $phoneNumber, string $mediaUrl, string $caption = '', string $type = 'image'): array;
    public function getMessageStatus(string $messageId): array;
    public function validateWebhook(array $payload): bool;
    public function isHealthy(): bool;
    public function getAllTemplates(): array;
    public function getUserMessages(string $phoneNumber, array $options = []): array;
}
