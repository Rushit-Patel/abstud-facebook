<?php

namespace App\Services;

use App\Contracts\WhatsappProviderInterface;
use App\Models\WhatsappProvider;
use App\Services\WhatsappProviders\InteraktProvider;
use App\Services\WhatsappProviders\GupshupProvider;
use App\Services\WhatsappProviders\GallaboxProvider;

class WhatsappProviderFactory
{
    public static function create(WhatsappProvider $provider): WhatsappProviderInterface
    {
        return match ($provider->slug) {
            'interakt' => new InteraktProvider($provider),
            'gupshup' => new GupshupProvider($provider),
            'gallabox' => new GallaboxProvider($provider),
            default => throw new \InvalidArgumentException("Unknown provider: {$provider->slug}")
        };
    }
}
