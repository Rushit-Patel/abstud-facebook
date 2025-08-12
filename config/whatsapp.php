<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default WhatsApp Provider
    |--------------------------------------------------------------------------
    |
    | This option defines the default WhatsApp provider that will be used
    | when no specific provider is requested. Set to null for auto-selection
    | based on priority.
    |
    */
    'default_provider' => env('WHATSAPP_DEFAULT_PROVIDER', null),
    
    /*
    |--------------------------------------------------------------------------
    | Retry Configuration
    |--------------------------------------------------------------------------
    |
    | Configure how many times failed messages should be retried and the
    | timeout for API requests.
    |
    */
    'retry_attempts' => env('WHATSAPP_RETRY_ATTEMPTS', 3),
    'timeout' => env('WHATSAPP_TIMEOUT', 30), // seconds
    
    /*
    |--------------------------------------------------------------------------
    | Rate Limiting
    |--------------------------------------------------------------------------
    |
    | Configure rate limiting settings for WhatsApp message sending.
    |
    */
    'rate_limiting' => [
        'enabled' => env('WHATSAPP_RATE_LIMITING', true),
        'default_limit' => env('WHATSAPP_RATE_LIMIT', 60), // per minute
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Webhook Configuration
    |--------------------------------------------------------------------------
    |
    | Configure webhook settings for receiving message status updates from
    | WhatsApp providers.
    |
    */
    'webhook' => [
        'enabled' => env('WHATSAPP_WEBHOOK_ENABLED', true),
        'url' => env('WHATSAPP_WEBHOOK_URL', '/api/whatsapp/webhook'),
        'verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | Logging Configuration
    |--------------------------------------------------------------------------
    |
    | Configure logging settings for WhatsApp operations.
    |
    */
    'logging' => [
        'enabled' => env('WHATSAPP_LOGGING', true),
        'level' => env('WHATSAPP_LOG_LEVEL', 'info'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Provider Specific Settings
    |--------------------------------------------------------------------------
    |
    | Provider-specific configuration that can be referenced by the
    | provider implementations.
    |
    */
    'providers' => [
        'interakt' => [
            'api_version' => 'v1',
            'base_url' => 'https://api.interakt.shop/v1/',
        ],
        'gupshup' => [
            'api_version' => 'v1',
            'base_url' => 'https://api.gupshup.io/sm/api/v1/',
        ],
        'gallabox' => [
            'api_version' => 'v1',
            'base_url' => 'https://api.gallabox.com/v1/',
        ],
    ],
];
